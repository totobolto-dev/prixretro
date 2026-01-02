<x-filament-panels::page>
    <div class="space-y-3">
        {{-- Compact Stats Bar --}}
        <div class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm flex gap-6 items-center flex-wrap">
            <span>Total: <strong>{{ $stats['total'] ?? 0 }}</strong></span>
            <span>Unclassified: <strong class="text-warning-400">{{ $stats['unclassified'] ?? 0 }}</strong></span>
            <span>Classified: <strong class="text-success-400">{{ $stats['classified'] ?? 0 }}</strong></span>
            <div class="ml-auto flex gap-3">
                <input type="text" wire:model.live.debounce.500ms="searchTerm" placeholder="Search..."
                       class="rounded px-2 py-1 text-xs bg-gray-700 border-gray-600 text-white placeholder-gray-400 w-48">
                <select wire:model.live="filterConsole" class="rounded px-2 py-1 text-xs bg-gray-700 border-gray-600 text-white">
                    <option value="">All Consoles</option>
                    @foreach($this->getConsoles() as $slug => $name)
                        <option value="{{ $slug }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if(count($listings) > 0)
            {{-- List View - Multiple Items --}}
            <div class="space-y-2">
                @foreach($listings as $index => $item)
                    <div class="bg-gray-900 border border-gray-700 rounded-lg p-3 hover:border-primary-500 transition-colors"
                         wire:key="listing-{{ $item['id'] }}">
                        <div class="flex gap-3 items-start">
                            {{-- Number --}}
                            <div class="text-gray-500 text-sm font-mono w-8 flex-shrink-0">
                                #{{ $index + 1 }}
                            </div>

                            {{-- Title and Info --}}
                            <div class="flex-1 min-w-0">
                                <a href="{{ $item['url'] }}" target="_blank" rel="noopener"
                                   class="text-white hover:text-primary-400 font-medium block mb-1">
                                    {{ $item['title'] }}
                                </a>
                                <div class="flex gap-3 text-xs text-gray-400">
                                    <span class="text-success-400 font-semibold">{{ number_format($item['price'], 2) }}€</span>
                                    <span>{{ $item['sold_date'] }}</span>
                                    <span>{{ $item['condition'] }}</span>
                                </div>
                            </div>

                            {{-- Variant Selector --}}
                            <div class="w-64 flex-shrink-0">
                                <select wire:change="updateItemVariant({{ $item['id'] }}, $event.target.value)"
                                        class="w-full rounded px-2 py-1.5 text-sm bg-gray-800 border-gray-600 text-white focus:border-primary-500 focus:ring-1 focus:ring-primary-500">
                                    <option value="">Select variant...</option>
                                    @foreach($this->getConsoles() as $consoleSlug => $consoleName)
                                        <optgroup label="{{ $consoleName }}">
                                            @foreach(\App\Models\Variant::where('console_id', \App\Models\Console::where('slug', $consoleSlug)->first()?->id)->get() as $variant)
                                                <option value="{{ $variant->id }}" @if($item['variant_id'] == $variant->id) selected @endif>
                                                    {{ $variant->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Quick Actions --}}
                            <div class="flex gap-1 flex-shrink-0">
                                <button wire:click="quickApprove({{ $item['id'] }})"
                                        class="px-3 py-1.5 bg-success-600 hover:bg-success-700 text-white text-sm font-medium rounded transition-colors"
                                        title="Keep (K)">
                                    Keep
                                </button>
                                <button wire:click="quickReject({{ $item['id'] }})"
                                        class="px-3 py-1.5 bg-danger-600 hover:bg-danger-700 text-white text-sm font-medium rounded transition-colors"
                                        title="Reject (R)">
                                    Reject
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Keyboard Shortcuts Info --}}
            <div class="bg-gray-800 text-gray-300 px-4 py-2 rounded-lg text-xs flex gap-4 items-center">
                <span class="font-semibold">Shortcuts:</span>
                <span><kbd class="px-1.5 py-0.5 bg-gray-700 rounded">K</kbd> Keep</span>
                <span><kbd class="px-1.5 py-0.5 bg-gray-700 rounded">R</kbd> Reject</span>
                <span><kbd class="px-1.5 py-0.5 bg-gray-700 rounded">Enter</kbd> Open eBay</span>
            </div>
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
