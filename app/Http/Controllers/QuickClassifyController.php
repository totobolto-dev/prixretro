<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class QuickClassifyController extends Controller
{
    public function index()
    {
        return view('admin.quick-classify');
    }

    public function getNextListing(Request $request)
    {
        // Get listings that need completeness classification
        $listing = Listing::where('status', 'approved')
            ->whereNull('completeness')
            ->orderBy('id')
            ->first();

        if (!$listing) {
            return response()->json(['done' => true]);
        }

        return response()->json([
            'id' => $listing->id,
            'title' => $listing->title,
            'price' => $listing->price,
            'image_url' => $listing->image_url,
            'url' => $listing->url,
            'variant' => $listing->variant ? $listing->variant->display_name : 'Non assigné',
            'console' => $listing->variant ? $listing->variant->console->name : null,
            'remaining' => Listing::where('status', 'approved')->whereNull('completeness')->count() - 1,
        ]);
    }

    public function classify(Request $request, Listing $listing)
    {
        $validated = $request->validate([
            'completeness' => 'required|in:loose,cib,sealed',
        ]);

        $listing->update([
            'completeness' => $validated['completeness'],
        ]);

        return response()->json(['success' => true]);
    }
}
