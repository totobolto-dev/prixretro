<?php

namespace App\Filament\Widgets;

use App\Services\EbayBrowseService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Filament\Actions\Action;

class EbayApiStats extends BaseWidget
{
    protected static ?int $sort = -10; // Show at top

    protected function getStats(): array
    {
        // Get saved stats from cache (never expires, manual refresh only)
        $stats = Cache::get('ebay_api_usage');
        $lastUpdated = Cache::get('ebay_api_usage_updated_at');

        if (!$stats) {
            return [
                Stat::make('eBay API Calls', 'Click to check')
                    ->description('Click "Refresh API Stats" to fetch usage data')
                    ->descriptionIcon('heroicon-o-information-circle')
                    ->color('gray')
                    ->extraAttributes([
                        'class' => 'cursor-pointer',
                        'wire:click' => '$refresh',
                    ]),
            ];
        }

        $usedCalls = $stats['used'] ?? 0;
        $limitCalls = $stats['limit'] ?? 5000;
        $remainingCalls = $limitCalls - $usedCalls;
        $percentageUsed = $limitCalls > 0 ? round(($usedCalls / $limitCalls) * 100, 1) : 0;

        $color = 'success';
        if ($percentageUsed > 80) {
            $color = 'danger';
        } elseif ($percentageUsed > 50) {
            $color = 'warning';
        }

        $updatedText = $lastUpdated ? 'Updated ' . \Carbon\Carbon::parse($lastUpdated)->diffForHumans() : '';

        return [
            Stat::make('eBay API Calls Remaining', number_format($remainingCalls))
                ->description("{$usedCalls} used of {$limitCalls} daily ({$percentageUsed}%) • {$updatedText}")
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color($color),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('refresh_stats')
                ->label('Refresh API Stats')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    $stats = $this->fetchApiStats();

                    if ($stats) {
                        Cache::forever('ebay_api_usage', $stats);
                        Cache::forever('ebay_api_usage_updated_at', now());

                        \Filament\Notifications\Notification::make()
                            ->title('API Stats Updated')
                            ->body("Remaining: " . (($stats['limit'] ?? 0) - ($stats['used'] ?? 0)) . " / " . ($stats['limit'] ?? 5000))
                            ->success()
                            ->send();
                    } else {
                        \Filament\Notifications\Notification::make()
                            ->title('Failed to fetch API stats')
                            ->body('Check eBay credentials in .env')
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function fetchApiStats(): ?array
    {
        $appId = config('services.ebay.app_id');
        $certId = config('services.ebay.cert_id');

        if (!$appId || !$certId) {
            Log::error('eBay credentials not configured');
            return null;
        }

        try {
            // Get OAuth token
            $credentials = base64_encode("{$appId}:{$certId}");
            $tokenResponse = Http::asForm()
                ->withHeaders([
                    'Authorization' => "Basic {$credentials}",
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->post('https://api.ebay.com/identity/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                    'scope' => 'https://api.ebay.com/oauth/api_scope',
                ]);

            if ($tokenResponse->failed()) {
                Log::error('Failed to get OAuth token for API stats');
                return null;
            }

            $token = $tokenResponse->json()['access_token'] ?? null;
            if (!$token) {
                return null;
            }

            // Get rate limit status
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$token}",
                'Accept' => 'application/json',
            ])->get('https://api.ebay.com/developer/analytics/v1/rate_limit/');

            if ($response->failed()) {
                Log::error('Failed to fetch API stats', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();

            // Find Browse API stats
            $resources = $data['rateLimits'] ?? [];
            foreach ($resources as $resource) {
                foreach ($resource['resources'] ?? [] as $res) {
                    if (str_contains($res['name'] ?? '', 'buy.browse')) {
                        $rates = $res['rates'] ?? [];
                        foreach ($rates as $rate) {
                            if ($rate['rateLimit'] ?? '' === 'per day') {
                                return [
                                    'used' => $rate['count'] ?? 0,
                                    'limit' => $rate['limit'] ?? 5000,
                                    'time_window' => $rate['timeWindow'] ?? 'day',
                                ];
                            }
                        }
                    }
                }
            }

            // Default fallback
            return [
                'used' => 0,
                'limit' => 5000,
                'time_window' => 'day',
            ];

        } catch (\Exception $e) {
            Log::error('Exception fetching API stats', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
