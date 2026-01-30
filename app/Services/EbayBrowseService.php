<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EbayBrowseService
{
    protected string $apiUrl;
    protected ?string $appId;
    protected ?string $certId;

    public function __construct()
    {
        $this->apiUrl = 'https://api.ebay.com/buy/browse/v1';
        $this->appId = config('services.ebay.app_id');
        $this->certId = config('services.ebay.cert_id');

        if (!$this->appId || !$this->certId) {
            Log::error('eBay API credentials not configured', [
                'app_id' => $this->appId ? 'set' : 'missing',
                'cert_id' => $this->certId ? 'set' : 'missing',
            ]);
        }
    }

    /**
     * Get OAuth access token (cached for ~2 hours)
     */
    protected function getAccessToken(): ?string
    {
        // Use file cache if database not available
        try {
            return Cache::remember('ebay_oauth_token', 7000, function () {
                return $this->fetchOAuthToken();
            });
        } catch (\Exception $e) {
            // Fallback: fetch without cache
            return $this->fetchOAuthToken();
        }
    }

    /**
     * Fetch OAuth token from eBay
     */
    protected function fetchOAuthToken(): ?string
    {
        if (!$this->appId || !$this->certId) {
            Log::error('Cannot fetch OAuth token: eBay credentials not configured');
            return null;
        }

        try {
            $credentials = base64_encode("{$this->appId}:{$this->certId}");

            $response = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic {$credentials}",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->post('https://api.ebay.com/identity/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope',
                ]);

            if ($response->failed()) {
                Log::error('eBay OAuth failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['access_token'] ?? null;

        } catch (\Exception $e) {
            Log::error('eBay OAuth exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Search for completed (sold) listings
     *
     * @param string $keywords Search keywords
     * @param int $limit Max results per page (1-200)
     * @param int $offset Offset for pagination
     * @return array
     */
    public function findCompletedItems(string $keywords, int $limit = 100, int $offset = 0): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['items' => [], 'error' => 'Failed to get OAuth token', 'total' => 0];
        }

        // Calculate date range (last 90 days of sold items)
        $endDate = now();
        $startDate = now()->subDays(90);

        $params = [
            'q' => $keywords,
            'limit' => min($limit, 200), // API max is 200
            'offset' => $offset,
            'filter' => implode(',', [
                'buyingOptions:{AUCTION|FIXED_PRICE}',
                'conditions:{USED|NEW}',
                'itemEndDate:[' . $startDate->toIso8601String() . '..' . $endDate->toIso8601String() . ']',
            ]),
            'sort' => 'endTimeSoonest',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'X-EBAY-C-MARKETPLACE-ID' => 'EBAY_FR',
            ])->get("{$this->apiUrl}/item_summary/search", $params);

            if ($response->failed()) {
                $errorMsg = sprintf('API request failed: HTTP %d - %s', $response->status(), $response->body());
                Log::error('eBay Browse API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'params' => $params,
                ]);
                return ['items' => [], 'error' => $errorMsg, 'total' => 0];
            }

            $data = $response->json();

            // Log first item for debugging
            if (!empty($data['itemSummaries'])) {
                Log::debug('Browse API First Item Sample', [
                    'item' => $data['itemSummaries'][0] ?? null,
                ]);
            }

            // Extract items from response
            $items = $data['itemSummaries'] ?? [];
            $total = $data['total'] ?? 0;
            $limit = $data['limit'] ?? $limit;
            $offset = $data['offset'] ?? $offset;

            return [
                'items' => $items,
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
                'totalPages' => $limit > 0 ? ceil($total / $limit) : 0,
                'currentPage' => $limit > 0 ? floor($offset / $limit) + 1 : 1,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('eBay Browse API exception', [
                'message' => $e->getMessage(),
                'keywords' => $keywords,
            ]);

            return ['items' => [], 'error' => $e->getMessage(), 'total' => 0];
        }
    }

    /**
     * Search active (current) listings
     *
     * @param string $keywords
     * @param int $limit
     * @param int $offset
     * @param float|null $minPrice Filter by minimum price
     * @param float|null $maxPrice Filter by maximum price
     * @return array
     */
    public function findActiveItems(string $keywords, int $limit = 100, int $offset = 0, ?float $minPrice = null, ?float $maxPrice = null): array
    {
        $token = $this->getAccessToken();
        if (!$token) {
            return ['items' => [], 'error' => 'Failed to get OAuth token', 'total' => 0];
        }

        $filters = [
            'buyingOptions:{AUCTION|FIXED_PRICE}',
            'conditions:{USED|NEW}',
            'categoryIds:139973', // Video Game Consoles category
        ];

        // Add price range if specified
        if ($minPrice !== null && $maxPrice !== null) {
            $filters[] = "price:[{$minPrice}..{$maxPrice}],priceCurrency:EUR";
        }

        $params = [
            'q' => $keywords,
            'limit' => min($limit, 200),
            'offset' => $offset,
            'filter' => implode(',', $filters),
            'sort' => 'price', // Lowest price first
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
                'X-EBAY-C-MARKETPLACE-ID' => 'EBAY_FR',
            ])->get("{$this->apiUrl}/item_summary/search", $params);

            if ($response->failed()) {
                return ['items' => [], 'error' => 'API request failed', 'total' => 0];
            }

            $data = $response->json();
            $items = $data['itemSummaries'] ?? [];
            $total = $data['total'] ?? 0;

            return [
                'items' => $items,
                'total' => $total,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('eBay Browse API active items search failed', ['error' => $e->getMessage()]);
            return ['items' => [], 'error' => $e->getMessage(), 'total' => 0];
        }
    }

    /**
     * Parse Browse API item into our Listing format
     *
     * @param array $item Raw eBay Browse API item
     * @return array|null Returns null for invalid items (0€ prices, etc.)
     */
    public function parseItem(array $item): ?array
    {
        $itemId = $item['itemId'] ?? null;
        $title = $item['title'] ?? 'Unknown';
        $itemWebUrl = $item['itemWebUrl'] ?? '';

        // Price (Browse API structure)
        $priceValue = (float) ($item['price']['value'] ?? 0);
        $priceCurrency = $item['price']['currency'] ?? 'EUR';

        // Convert to EUR if needed (most should already be EUR from EBAY_FR)
        $price = $priceCurrency === 'EUR' ? $priceValue : $priceValue;

        // Skip 0€ items (invalid/incomplete data)
        if ($price <= 0) {
            return null;
        }

        // Item end date (for sold items)
        $itemEndDate = $item['itemEndDate'] ?? null;
        $soldDate = $itemEndDate ? \Carbon\Carbon::parse($itemEndDate) : null;

        // Condition
        $condition = $item['condition'] ?? 'Unknown';
        $conditionId = $item['conditionId'] ?? null;

        // Shipping cost
        $shippingCost = (float) ($item['shippingOptions'][0]['shippingCost']['value'] ?? 0);

        // Item location (to identify EBAY_FR items)
        $itemLocation = $item['itemLocation']['country'] ?? null;

        // Thumbnail image
        $thumbnailUrl = $item['image']['imageUrl'] ?? null;

        $result = [
            'ebay_item_id' => $itemId,
            'title' => $title,
            'price' => $price,
            'sold_date' => $soldDate,
            'url' => $itemWebUrl,
            'source' => 'ebay',
            'item_condition' => $condition,
            'condition_id' => $conditionId,
            'shipping_cost' => $shippingCost,
            'status' => 'pending', // Will be classified later
        ];

        if ($thumbnailUrl) {
            $result['thumbnail_url'] = $thumbnailUrl;
        }

        return $result;
    }
}
