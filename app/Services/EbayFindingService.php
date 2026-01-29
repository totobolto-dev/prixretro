<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EbayFindingService
{
    protected string $apiUrl;
    protected string $appId;
    protected int $siteId;

    public function __construct()
    {
        $this->apiUrl = config('services.ebay.finding_api_url');
        $this->appId = config('services.ebay.app_id');
        $this->siteId = config('services.ebay.site_id');
    }

    /**
     * Search for completed (sold) listings
     *
     * @param string $keywords Search keywords
     * @param int $maxResults Max results per page (1-100)
     * @param int $pageNumber Page number (1-based)
     * @return array
     */
    public function findCompletedItems(string $keywords, int $maxResults = 100, int $pageNumber = 1): array
    {
        $params = [
            'OPERATION-NAME' => 'findCompletedItems',
            'SERVICE-VERSION' => '1.13.0',
            'SECURITY-APPNAME' => $this->appId,
            'RESPONSE-DATA-FORMAT' => 'JSON',
            'REST-PAYLOAD' => '',
            'keywords' => $keywords,
            'paginationInput.entriesPerPage' => $maxResults,
            'paginationInput.pageNumber' => $pageNumber,
            'GLOBAL-ID' => 'EBAY-FR', // Force eBay France
            'itemFilter(0).name' => 'SoldItemsOnly',
            'itemFilter(0).value' => 'true',
            'itemFilter(1).name' => 'ListingType',
            'itemFilter(1).value(0)' => 'FixedPrice',
            'itemFilter(1).value(1)' => 'Auction',
            'sortOrder' => 'EndTimeSoonest',
        ];

        try {
            // Retry logic for rate limit errors (eBay recommends up to 2 retries)
            $maxRetries = 2;
            $retryDelay = 2; // seconds
            $response = null;

            for ($attempt = 0; $attempt <= $maxRetries; $attempt++) {
                if ($attempt > 0) {
                    Log::info("eBay API retry attempt {$attempt}/{$maxRetries}", ['keywords' => $keywords]);
                    sleep($retryDelay);
                    $retryDelay *= 2; // Exponential backoff
                }

                $response = Http::timeout(30)->get($this->apiUrl, $params);

                // Check if it's a rate limit error (Error 10001)
                if ($response->status() === 500) {
                    $body = $response->json();
                    $errorId = $body['errorMessage'][0]['error'][0]['errorId'][0] ?? null;

                    if ($errorId === '10001' && $attempt < $maxRetries) {
                        Log::warning('eBay rate limit hit, retrying...', ['attempt' => $attempt + 1]);
                        continue; // Retry
                    }
                }

                // Success or non-retryable error
                break;
            }

            if ($response->failed()) {
                $errorMsg = sprintf('API request failed: HTTP %d - %s', $response->status(), $response->body());
                Log::error('eBay API request failed after retries', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->apiUrl,
                    'params' => $params,
                    'retries' => $maxRetries,
                ]);
                return ['items' => [], 'error' => $errorMsg];
            }

            $data = $response->json();

            // Check for API errors
            if (isset($data['errorMessage'])) {
                Log::error('eBay API error', ['error' => $data['errorMessage']]);
                return ['items' => [], 'error' => $data['errorMessage']];
            }

            // Extract items from response
            $findItemsResponse = $data['findCompletedItemsResponse'][0] ?? [];
            $ack = $findItemsResponse['ack'][0] ?? '';

            if ($ack !== 'Success') {
                Log::warning('eBay API non-success ack', ['ack' => $ack, 'response' => $findItemsResponse]);
                return ['items' => [], 'error' => 'Non-success acknowledgement'];
            }

            $searchResult = $findItemsResponse['searchResult'][0] ?? [];
            $items = $searchResult['item'] ?? [];
            $totalEntries = (int) ($searchResult['@count'] ?? 0);
            $totalPages = (int) ($findItemsResponse['paginationOutput'][0]['totalPages'][0] ?? 1);

            return [
                'items' => $items,
                'totalEntries' => $totalEntries,
                'totalPages' => $totalPages,
                'currentPage' => $pageNumber,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('eBay API exception', [
                'message' => $e->getMessage(),
                'keywords' => $keywords,
            ]);

            return ['items' => [], 'error' => $e->getMessage()];
        }
    }

    /**
     * Parse eBay item into our Listing format
     *
     * @param array $item Raw eBay item from API
     * @return array
     */
    public function parseItem(array $item): array
    {
        $itemId = $item['itemId'][0] ?? null;
        $title = $item['title'][0] ?? 'Unknown';
        $viewItemURL = $item['viewItemURL'][0] ?? '';

        // Price
        $price = (float) ($item['sellingStatus'][0]['currentPrice'][0]['__value__'] ?? 0);

        // Sold date
        $endTime = $item['listingInfo'][0]['endTime'][0] ?? null;
        $soldDate = $endTime ? \Carbon\Carbon::parse($endTime) : null;

        // Condition
        $conditionId = $item['condition'][0]['conditionId'][0] ?? null;
        $conditionDisplayName = $item['condition'][0]['conditionDisplayName'][0] ?? 'Unknown';

        // Shipping cost (if available)
        $shippingCost = (float) ($item['shippingInfo'][0]['shippingServiceCost'][0]['__value__'] ?? 0);

        return [
            'ebay_item_id' => $itemId,
            'title' => $title,
            'price' => $price,
            'sold_date' => $soldDate,
            'url' => $viewItemURL,
            'source' => 'ebay',
            'item_condition' => $conditionDisplayName,
            'condition_id' => $conditionId,
            'shipping_cost' => $shippingCost,
            'status' => 'pending', // Will be classified later
        ];
    }

    /**
     * Search active (current) listings
     *
     * @param string $keywords
     * @param int $maxResults
     * @return array
     */
    public function findActiveItems(string $keywords, int $maxResults = 100): array
    {
        $params = [
            'OPERATION-NAME' => 'findItemsByKeywords',
            'SERVICE-VERSION' => '1.13.0',
            'SECURITY-APPNAME' => $this->appId,
            'RESPONSE-DATA-FORMAT' => 'JSON',
            'REST-PAYLOAD' => '',
            'keywords' => $keywords,
            'paginationInput.entriesPerPage' => $maxResults,
            'GLOBAL-ID' => 'EBAY-FR',
            'sortOrder' => 'PricePlusShippingLowest',
        ];

        try {
            $response = Http::timeout(30)->get($this->apiUrl, $params);

            if ($response->failed()) {
                return ['items' => [], 'error' => 'API request failed'];
            }

            $data = $response->json();
            $findItemsResponse = $data['findItemsByKeywordsResponse'][0] ?? [];
            $searchResult = $findItemsResponse['searchResult'][0] ?? [];
            $items = $searchResult['item'] ?? [];

            return [
                'items' => $items,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('eBay active items search failed', ['error' => $e->getMessage()]);
            return ['items' => [], 'error' => $e->getMessage()];
        }
    }
}
