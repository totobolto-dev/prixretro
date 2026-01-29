<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class EbayCheckRateLimits extends Command
{
    protected $signature = 'ebay:check-rate-limits';
    protected $description = 'Check eBay API rate limit status using Analytics API';

    public function handle(): int
    {
        $this->info('🔍 Checking eBay API Rate Limits...');
        $this->newLine();

        // Step 1: Get OAuth access token using client credentials flow
        $this->info('Step 1: Getting OAuth access token...');
        $token = $this->getOAuthToken();

        if (!$token) {
            $this->error('❌ Failed to get OAuth token');
            return Command::FAILURE;
        }

        $this->info('✅ Token acquired');
        $this->newLine();

        // Step 2: Call Analytics API to check rate limits
        $this->info('Step 2: Fetching rate limit data...');
        $rateLimitData = $this->getRateLimits($token);

        if (!$rateLimitData) {
            $this->error('❌ Failed to fetch rate limit data');
            return Command::FAILURE;
        }

        // Step 3: Display results
        $this->displayRateLimits($rateLimitData);

        return Command::SUCCESS;
    }

    /**
     * Get OAuth access token using client credentials flow
     */
    private function getOAuthToken(): ?string
    {
        $appId = config('services.ebay.app_id');
        $certId = config('services.ebay.cert_id');

        // Base64 encode credentials
        $credentials = base64_encode("{$appId}:{$certId}");

        try {
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
                $this->error('OAuth request failed:');
                $this->line($response->body());
                Log::error('eBay OAuth failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            return $data['access_token'] ?? null;

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            Log::error('eBay OAuth exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Get rate limit data from Analytics API
     */
    private function getRateLimits(string $token): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])->get('https://api.ebay.com/developer/analytics/v1_beta/rate_limit/');

            if ($response->failed()) {
                $this->error('Analytics API request failed:');
                $this->line($response->body());
                Log::error('eBay Analytics API failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json();

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            Log::error('eBay Analytics API exception', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Display rate limit information
     */
    private function displayRateLimits(array $data): void
    {
        $this->newLine();
        $this->info('📊 eBay API Rate Limit Status');
        $this->info('═══════════════════════════════════════');
        $this->newLine();

        // Check if we have rate limit data
        if (empty($data['rateLimits'])) {
            $this->warn('⚠️  No rate limit data returned');
            $this->line('Full response:');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));
            return;
        }

        // Display rate limits for each API
        foreach ($data['rateLimits'] as $apiLimit) {
            $apiName = $apiLimit['apiName'] ?? 'Unknown';
            $apiContext = $apiLimit['apiContext'] ?? 'Unknown';

            $this->line("API: <fg=cyan>{$apiName}</> (Context: {$apiContext})");

            foreach ($apiLimit['resources'] as $resource) {
                $name = $resource['name'] ?? 'Unknown';
                $rates = $resource['rates'] ?? [];

                $this->line("  Resource: <fg=yellow>{$name}</>");

                foreach ($rates as $rate) {
                    $limit = $rate['limit'] ?? 'N/A';
                    $remaining = $rate['remaining'] ?? 'N/A';
                    $used = $limit !== 'N/A' && $remaining !== 'N/A'
                        ? ($limit - $remaining)
                        : 'N/A';
                    $resetTime = $rate['timeWindowStartTime'] ?? null;
                    $window = $rate['timeWindow'] ?? 'Unknown';

                    // Color code based on usage
                    $percentUsed = $limit > 0 ? ($used / $limit) * 100 : 0;
                    $color = $percentUsed > 90 ? 'red' : ($percentUsed > 50 ? 'yellow' : 'green');

                    $this->line("    Time Window: {$window}");
                    $this->line("    Limit: {$limit}");
                    $this->line("    Used: <fg={$color}>{$used}</>");
                    $this->line("    Remaining: <fg={$color}>{$remaining}</>");

                    if ($resetTime) {
                        $this->line("    Resets at: {$resetTime}");
                    }

                    $this->newLine();
                }
            }

            $this->line('───────────────────────────────────────');
            $this->newLine();
        }

        // Check for Finding API specifically
        $findingApiFound = false;
        foreach ($data['rateLimits'] as $apiLimit) {
            if (stripos($apiLimit['apiName'] ?? '', 'finding') !== false) {
                $findingApiFound = true;
                break;
            }
        }

        if (!$findingApiFound) {
            $this->warn('⚠️  Finding API not found in rate limit data!');
            $this->line('This might explain why you\'re getting rate limit errors.');
            $this->line('Possible reasons:');
            $this->line('  1. Finding API not provisioned for your application');
            $this->line('  2. Finding API uses different rate limiting (not tracked by Analytics API)');
            $this->line('  3. Application needs additional approval for Finding API');
        }
    }
}
