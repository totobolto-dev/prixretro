<x-filament-panels::page>
    <div class="mb-4">
        <p class="text-sm text-gray-600 dark:text-gray-400">
            This page shows all consoles and their guide coverage. Consoles with a green checkmark have buying guides, while red X marks indicate missing guides.
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            <strong>Goal:</strong> Create a comprehensive buying guide for every console. Use the filter to quickly find consoles that need guides.
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            <strong>Note:</strong> To add a new guide, create the guide page and update the guide map in <code>app/Filament/Pages/ManageGuides.php</code> (getGuideMap method).
        </p>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
