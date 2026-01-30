<x-filament-panels::page>
    <div class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            This page shows all variants that have approved sold listings. Use the "Fetch Now" button to update current listings for any variant.
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            <strong>Priority:</strong> Variants shown at the top have never been fetched or were fetched longest ago.
        </p>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
