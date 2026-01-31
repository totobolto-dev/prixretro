<x-filament-panels::page>
    <div class="mb-4 space-y-2">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <strong>Unified Current Listings Manager:</strong> View, approve, reject, and manage eBay listings with thumbnails and grouping by console.
        </p>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ \App\Models\CurrentListing::where('status', 'approved')->where('is_sold', false)->count() }}</div>
                <div class="text-xs text-gray-500">Active Listings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ \App\Models\CurrentListing::where('status', 'pending')->count() }}</div>
                <div class="text-xs text-gray-500">Pending Review</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ \App\Models\Variant::whereHas('currentListings', fn($q) => $q->where('status', 'approved')->where('is_sold', false))->count() }}</div>
                <div class="text-xs text-gray-500">Variants Covered</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">5,000</div>
                <div class="text-xs text-gray-500">API Calls/Day (eBay)</div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
