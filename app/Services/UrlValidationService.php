<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UrlValidationService
{
    /**
     * Validate an eBay listing URL by checking for redirects
     *
     * @param string $url The eBay URL to validate
     * @param string|null $expectedItemId The expected eBay item ID
     * @return array ['status' => string, 'redirect_target' => string|null, 'error' => string|null]
     */
    public function validateUrl(string $url, ?string $expectedItemId = null): array
    {
        try {
            // Extract expected item ID from URL if not provided
            if (!$expectedItemId) {
                $expectedItemId = $this->extractItemId($url);
            }

            // Track the effective URL after redirects
            $effectiveUrl = null;

            // Use HEAD request to minimize load on eBay
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => $this->getRandomUserAgent(),
                    'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->withOptions([
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => true,
                    ],
                    'on_stats' => function ($stats) use (&$effectiveUrl) {
                        $effectiveUrl = (string) $stats->getEffectiveUri();
                    },
                ])
                ->head($url);

            // Get final URL (either from stats or original URL)
            $finalUrl = $effectiveUrl ?? $url;

            // Check if we got a CAPTCHA challenge
            if ($this->isCaptchaChallenge($finalUrl, $response)) {
                return [
                    'status' => 'captcha',
                    'redirect_target' => null,
                    'error' => 'CAPTCHA challenge detected - cannot validate URL',
                ];
            }

            // If we were redirected, extract the new item ID
            if ($finalUrl !== $url) {
                $redirectedItemId = $this->extractItemId($finalUrl);

                // Check if redirect is to a different item
                if ($redirectedItemId && $expectedItemId && $redirectedItemId !== $expectedItemId) {
                    return [
                        'status' => 'invalid',
                        'redirect_target' => $finalUrl,
                        'error' => "URL redirects to different item: {$redirectedItemId} (expected: {$expectedItemId})",
                    ];
                }

                // Check if redirect is to homepage or error page
                if ($this->isErrorRedirect($finalUrl)) {
                    return [
                        'status' => 'invalid',
                        'redirect_target' => $finalUrl,
                        'error' => 'URL redirects to error/homepage (item likely removed)',
                    ];
                }
            }

            // Check HTTP status
            if ($response->status() === 404) {
                return [
                    'status' => 'invalid',
                    'redirect_target' => null,
                    'error' => 'Item not found (404)',
                ];
            }

            if (!$response->successful()) {
                return [
                    'status' => 'error',
                    'redirect_target' => null,
                    'error' => "HTTP error: {$response->status()}",
                ];
            }

            // URL is valid
            return [
                'status' => 'valid',
                'redirect_target' => $finalUrl !== $url ? $finalUrl : null,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('URL validation failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'redirect_target' => null,
                'error' => "Validation error: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Extract eBay item ID from URL
     *
     * @param string $url
     * @return string|null
     */
    private function extractItemId(string $url): ?string
    {
        // Match patterns like /itm/123456789 or /itm/title-here/123456789
        if (preg_match('/\/itm\/(?:[^\/]+\/)?(\d+)/i', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Check if response indicates a CAPTCHA challenge
     *
     * @param string $finalUrl
     * @param \Illuminate\Http\Client\Response $response
     * @return bool
     */
    private function isCaptchaChallenge(string $finalUrl, $response): bool
    {
        // Check for common eBay CAPTCHA URLs
        $captchaIndicators = [
            '/splashui/captcha',
            '/signin/captcha',
            'sec/captcha',
            '/verify',
            'challenge',
        ];

        foreach ($captchaIndicators as $indicator) {
            if (stripos($finalUrl, $indicator) !== false) {
                return true;
            }
        }

        // Check for specific HTTP status codes that might indicate CAPTCHA
        if ($response->status() === 503) {
            return true; // Service Unavailable often used for CAPTCHA
        }

        return false;
    }

    /**
     * Check if URL redirect indicates an error (homepage, category page, etc.)
     *
     * @param string $url
     * @return bool
     */
    private function isErrorRedirect(string $url): bool
    {
        // Redirects to homepage or category pages indicate removed item
        $errorPatterns = [
            'ebay\.fr/?$',           // Homepage
            'ebay\.fr/\?',           // Homepage with params
            'ebay\.fr/b/',           // Category browse
            'ebay\.fr/sch/',         // Search page
            'ebay\.fr/n/',           // Category hub
        ];

        foreach ($errorPatterns as $pattern) {
            if (preg_match("/{$pattern}/i", $url)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a random User-Agent to avoid bot detection
     *
     * @return string
     */
    private function getRandomUserAgent(): string
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        ];

        return $userAgents[array_rand($userAgents)];
    }

    /**
     * Add a delay between requests to avoid triggering rate limits
     *
     * @param int $milliseconds
     * @return void
     */
    public function throttle(int $milliseconds = 1000): void
    {
        usleep($milliseconds * 1000);
    }
}
