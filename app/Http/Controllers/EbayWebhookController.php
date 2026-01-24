<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EbayWebhookController extends Controller
{
    /**
     * Handle eBay marketplace account deletion notifications
     *
     * This endpoint is required by eBay's privacy compliance, even though
     * PrixRetro doesn't store eBay user data or use OAuth.
     */
    public function handleAccountDeletion(Request $request)
    {
        // Get verification token from config
        $expectedToken = config('services.ebay.verification_token');

        // eBay sends a challenge parameter for endpoint verification
        if ($request->has('challenge_code')) {
            $challengeCode = $request->input('challenge_code');

            // Verify the endpoint URL with challenge response
            return response()->json([
                'challengeResponse' => hash('sha256', $challengeCode . $expectedToken . $request->url())
            ]);
        }

        // Handle actual deletion notification
        $notification = $request->all();

        // Log the notification (we don't actually need to do anything with it)
        Log::info('eBay account deletion notification received', [
            'notification' => $notification,
            'timestamp' => now()->toIso8601String()
        ]);

        // PrixRetro doesn't store eBay user data, so nothing to delete
        // Just acknowledge receipt with HTTP 200
        return response()->json([
            'status' => 'acknowledged',
            'message' => 'Notification received - no user data stored'
        ], 200);
    }
}
