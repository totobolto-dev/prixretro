<?php

namespace App\Http\Controllers;

use App\Models\UserCollection;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $collection = $user->collection()
            ->with(['variant.console', 'variant.listings' => function($query) {
                $query->where('status', 'approved');
            }])
            ->get();

        $totalValue = 0;
        $totalPurchasePrice = 0;

        foreach ($collection as $item) {
            $currentValue = $item->getCurrentValue();
            if ($currentValue) {
                $totalValue += $currentValue;
            }
            if ($item->purchase_price) {
                $totalPurchasePrice += $item->purchase_price;
            }
        }

        $profitLoss = $totalValue - $totalPurchasePrice;

        return view('collection.index', compact('collection', 'totalValue', 'totalPurchasePrice', 'profitLoss'));
    }

    public function add(Request $request, Variant $variant)
    {
        $user = Auth::user();

        // Check if already in collection
        $exists = $user->collection()->where('variant_id', $variant->id)->exists();

        if ($exists) {
            return back()->with('error', 'Cette console est déjà dans votre collection');
        }

        $user->collection()->create([
            'variant_id' => $variant->id,
        ]);

        return back()->with('success', 'Console ajoutée à votre collection');
    }

    public function remove(UserCollection $collection)
    {
        // Ensure user owns this collection item
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $collection->delete();

        return back()->with('success', 'Console retirée de votre collection');
    }

    public function update(Request $request, UserCollection $collection)
    {
        // Ensure user owns this collection item
        if ($collection->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'completeness' => 'nullable|in:loose,cib,sealed',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchase_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $collection->update($validated);

        return back()->with('success', 'Console mise à jour');
    }
}
