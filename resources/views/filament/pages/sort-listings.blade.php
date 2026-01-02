<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Unsorted</div>
                <div class="text-2xl font-bold">{{ $stats['total'] ?? 0 }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Unclassified</div>
                <div class="text-2xl font-bold text-warning-500">{{ $stats['unclassified'] ?? 0 }}</div>
            </x-filament::card>
            <x-filament::card>
                <div class="text-sm text-gray-500 dark:text-gray-400">Classified (Pending Variant)</div>
                <div class="text-2xl font-bold text-success-500">{{ $stats['classified'] ?? 0 }}</div>
            </x-filament::card>
        </div>

        {{-- Filters --}}
        <x-filament::card>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Status</label>
                    <select wire:model.live="filterStatus" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="all">All</option>
                        <option value="unclassified">Unclassified</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Console</label>
                    <select wire:model.live="filterConsole" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="">All Consoles</option>
                        @foreach($this->getConsoles() as $slug => $name)
                            <option value="{{ $slug }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Search</label>
                    <input type="text" wire:model.live.debounce.500ms="searchTerm" placeholder="Search title..."
                           class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
        </x-filament::card>

        @if(count($listings) > 0)
            @php
                $item = $listings[$currentIndex] ?? null;
            @endphp

            @if($item)
                {{-- Current Item --}}
                <x-filament::card>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">
                                Item {{ $currentIndex + 1 }} of {{ count($listings) }}
                            </h3>
                            <div class="text-sm text-gray-500">
                                ID: {{ $item['item_id'] }}
                            </div>
                        </div>

                        {{-- Item Info --}}
                        <div class="border-l-4 border-primary-500 pl-4 py-2 bg-gray-50 dark:bg-gray-800 rounded">
                            <div class="flex gap-4 items-start">
                                @if($item['thumbnail_url'])
                                    <img src="{{ $item['thumbnail_url'] }}" alt="Thumbnail" class="w-24 h-24 object-cover rounded-lg flex-shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $item['title'] }}
                                    </div>
                                    <div class="flex gap-4 mb-2 text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-semibold text-success-600 dark:text-success-400 text-base">{{ number_format($item['price'], 2) }}€</span>
                                        <span>{{ $item['sold_date'] }}</span>
                                        <span>{{ $item['condition'] }}</span>
                                    </div>
                                    <div class="flex gap-2 items-center">
                                        <a href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            View on eBay
                                        </a>
                                        @if($item['console_slug'])
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-primary-100 text-primary-800 dark:bg-primary-800 dark:text-primary-100">
                                                Console: {{ $item['console_slug'] }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Classification Form --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Console Type</label>
                                <select wire:model.live="selectedConsole"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">Select Console...</option>
                                    @foreach($this->getConsoles() as $slug => $name)
                                        <option value="{{ $slug }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">
                                    Variant
                                    @if(!$selectedConsole)
                                        <span class="text-xs text-gray-500 font-normal">(Select console first)</span>
                                    @endif
                                </label>
                                <select wire:model="selectedVariant"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        :class="{ 'dark:bg-gray-700': @js($selectedConsole), 'dark:bg-gray-800': !@js($selectedConsole) }"
                                        @if(!$selectedConsole) disabled @endif>
                                    <option value="">Select Variant...</option>
                                    @foreach($this->getVariants() as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2 flex-wrap">
                            <x-filament::button
                                wire:click="classifyItem({{ $item['id'] }})"
                                color="success"
                                size="lg"
                                :disabled="!$selectedConsole">
                                ✓ Save & Next
                            </x-filament::button>

                            <x-filament::button
                                wire:click="classifyItem({{ $item['id'] }})"
                                wire:click.prevent="$set('selectedStatus', 'reject')"
                                color="danger"
                                size="lg">
                                ✗ Reject & Next
                            </x-filament::button>

                            <x-filament::button
                                wire:click="skipItem"
                                color="gray"
                                size="lg">
                                → Skip
                            </x-filament::button>

                            @if($currentIndex > 0)
                                <x-filament::button
                                    wire:click="previousItem"
                                    color="gray"
                                    size="lg">
                                    ← Previous
                                </x-filament::button>
                            @endif>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                            <div class="bg-primary-600 h-2.5 rounded-full"
                                 style="width: {{ count($listings) > 0 ? (($currentIndex + 1) / count($listings)) * 100 : 0 }}%"></div>
                        </div>
                        <div class="text-center text-sm text-gray-500">
                            {{ round(count($listings) > 0 ? (($currentIndex + 1) / count($listings)) * 100 : 0, 1) }}% Complete
                        </div>
                    </div>
                </x-filament::card>

                {{-- Keyboard Shortcuts Help --}}
                <x-filament::card class="bg-gray-50 dark:bg-gray-800">
                    <div class="text-sm space-y-1">
                        <div class="font-semibold mb-2">Keyboard Shortcuts (Desktop):</div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-xs">
                            <div><kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded">Enter</kbd> Save</div>
                            <div><kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded">Space</kbd> Skip</div>
                            <div><kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded">←</kbd> Previous</div>
                            <div><kbd class="px-2 py-1 bg-gray-200 dark:bg-gray-700 rounded">R</kbd> Reject</div>
                        </div>
                    </div>
                </x-filament::card>
            @endif
        @else
            <x-filament::card>
                <div class="text-center py-12">
                    <div class="text-4xl mb-4">🎉</div>
                    <h3 class="text-lg font-semibold mb-2">All Done!</h3>
                    <p class="text-gray-500">No items to classify with current filters.</p>
                </div>
            </x-filament::card>
        @endif
    </div>
</x-filament-panels::page>
