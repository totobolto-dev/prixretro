<x-filament-panels::page>
    <div class="mb-4 space-y-2">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            <strong>🎯 Unified Hub:</strong> Fetch, approve, reject, and manage eBay listings. Click "Fetch" on any row to grab listings for that variant.
        </p>
        <div wire:poll.10s="updateStats" class="grid grid-cols-1 md:grid-cols-4 gap-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $activeCount }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Active Listings</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Pending Review</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $variantsCovered }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Variants Covered</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600 dark:text-gray-300">5,000</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">API Calls/Day (eBay)</div>
            </div>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
