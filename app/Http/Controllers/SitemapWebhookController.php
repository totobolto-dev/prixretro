<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SitemapWebhookController extends Controller
{
    /**
     * Regenerate sitemap via webhook (triggered by GitHub Actions)
     *
     * This endpoint allows GitHub Actions to trigger sitemap regeneration
     * without needing direct database access. Protected by secret token.
     */
    public function regenerate(Request $request)
    {
        // Get expected token from environment
        $expectedToken = config('app.sitemap_token');

        // Verify the token
        $providedToken = $request->header('X-Sitemap-Token') ?? $request->input('token');

        if (!$providedToken || !hash_equals($expectedToken, $providedToken)) {
            Log::warning('Unauthorized sitemap regeneration attempt', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        try {
            // Run the sitemap generation command
            Artisan::call('sitemap:generate');
            $output = Artisan::output();

            Log::info('Sitemap regenerated via webhook', [
                'triggered_by' => 'github_actions',
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Sitemap regenerated successfully',
                'output' => trim($output),
                'timestamp' => now()->toIso8601String(),
            ], 200);

        } catch (\Exception $e) {
            Log::error('Sitemap regeneration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to regenerate sitemap',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
