<x-filament-panels::page>
    @if($isDone)
        <div class="text-center py-16">
            <div class="text-6xl mb-4">🎉</div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Terminé !</h2>
            <p class="text-gray-600 dark:text-gray-400">Toutes les annonces en attente ont été traitées.</p>
            <a href="{{ route('filament.admin.resources.listings.index') }}" class="mt-4 inline-block text-primary-600 hover:underline">
                ← Retour aux listings
            </a>
        </div>
    @elseif($currentListing)
        <div class="space-y-6">
            {{-- Header with counter --}}
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                    Classification rapide
                </h2>
                <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">
                    {{ $remainingCount }} restantes
                </div>
            </div>

            {{-- Main content --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Image Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow">
                    @if($currentListing->thumbnail_url)
                        <img src="{{ $currentListing->thumbnail_url }}"
                             alt="{{ $currentListing->title }}"
                             class="w-full h-96 object-contain rounded-lg bg-gray-100 dark:bg-gray-900">
                    @else
                        <div class="w-full h-96 flex items-center justify-center bg-gray-100 dark:bg-gray-900 rounded-lg">
                            <span class="text-gray-400 dark:text-gray-600">Pas d'image</span>
                        </div>
                    @endif

                    <div class="mt-4 space-y-2">
                        <h3 class="font-semibold text-gray-900 dark:text-white text-lg">
                            {{ $currentListing->title }}
                        </h3>
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-green-600 dark:text-green-400">
                                {{ number_format($currentListing->price, 2) }}€
                            </span>
                            <a href="{{ $currentListing->url }}"
                               target="_blank"
                               class="text-primary-600 hover:underline text-sm">
                                Voir sur eBay →
                            </a>
                        </div>
                        @if($currentListing->sold_date)
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Vendu le {{ $currentListing->sold_date->format('d/m/Y') }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Form Section --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Classification</h3>

                    {{-- Console Select --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Console
                        </label>
                        <select wire:model.live="selectedConsole"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                            <option value="">-- Sélectionner --</option>
                            @foreach($this->getConsoles() as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Variant Select --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Variante
                        </label>
                        <select wire:model="selectedVariant"
                                @if(!$selectedConsole) disabled @endif
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50">
                            <option value="">-- Variante par défaut --</option>
                            @foreach($this->getVariants() as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Laissez vide pour utiliser la variante par défaut, ou créez-en une ci-dessous
                        </p>
                    </div>

                    {{-- New Variant --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Ou créer une nouvelle variante
                        </label>
                        <input type="text"
                               wire:model="newVariantName"
                               @if(!$selectedConsole) disabled @endif
                               placeholder="Ex: Atomic Purple"
                               class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-50">
                    </div>

                    {{-- Completeness --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Complétude
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button"
                                    wire:click="$set('selectedCompleteness', 'loose')"
                                    class="px-4 py-3 text-sm font-semibold rounded-lg border-2 transition
                                           {{ $selectedCompleteness === 'loose' ? 'border-gray-500 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-gray-400' }}">
                                <div class="text-2xl mb-1">⚪</div>
                                Loose
                            </button>
                            <button type="button"
                                    wire:click="$set('selectedCompleteness', 'cib')"
                                    class="px-4 py-3 text-sm font-semibold rounded-lg border-2 transition
                                           {{ $selectedCompleteness === 'cib' ? 'border-blue-500 bg-blue-100 dark:bg-blue-900 text-blue-900 dark:text-blue-100' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-blue-400' }}">
                                <div class="text-2xl mb-1">📦</div>
                                CIB
                            </button>
                            <button type="button"
                                    wire:click="$set('selectedCompleteness', 'sealed')"
                                    class="px-4 py-3 text-sm font-semibold rounded-lg border-2 transition
                                           {{ $selectedCompleteness === 'sealed' ? 'border-orange-500 bg-orange-100 dark:bg-orange-900 text-orange-900 dark:text-orange-100' : 'border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-400 hover:border-orange-400' }}">
                                <div class="text-2xl mb-1">🔒</div>
                                Sealed
                            </button>
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Loose = console seule | CIB = complet boîte | Sealed = neuf scellé
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-2 gap-3">
                            <button type="button"
                                    wire:click="approve"
                                    class="px-6 py-4 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 active:translate-y-0">
                                <div class="text-2xl mb-1">✓</div>
                                Approuver
                            </button>
                            <button type="button"
                                    wire:click="reject"
                                    class="px-6 py-4 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 active:translate-y-0">
                                <div class="text-2xl mb-1">✗</div>
                                Rejeter
                            </button>
                            <button type="button"
                                    wire:click="hold"
                                    class="px-6 py-4 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 active:translate-y-0">
                                <div class="text-2xl mb-1">⏸</div>
                                Hold
                            </button>
                            <button type="button"
                                    wire:click="skip"
                                    class="px-6 py-4 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-lg hover:shadow-xl transition transform hover:-translate-y-0.5 active:translate-y-0">
                                <div class="text-2xl mb-1">→</div>
                                Skip
                            </button>
                        </div>
                    </div>

                    {{-- Keyboard shortcuts hint --}}
                    <div class="pt-2 text-xs text-center text-gray-500 dark:text-gray-400">
                        Raccourcis: <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">A</kbd> Approuver •
                        <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">R</kbd> Rejeter •
                        <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">H</kbd> Hold •
                        <kbd class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">S</kbd> Skip
                    </div>
                </div>
            </div>
        </div>

        {{-- Keyboard shortcuts --}}
        <script>
            document.addEventListener('keydown', function(e) {
                // Ignore if typing in input/textarea
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                    return;
                }

                switch(e.key.toLowerCase()) {
                    case 'a':
                        e.preventDefault();
                        @this.approve();
                        break;
                    case 'r':
                        e.preventDefault();
                        @this.reject();
                        break;
                    case 'h':
                        e.preventDefault();
                        @this.hold();
                        break;
                    case 's':
                        e.preventDefault();
                        @this.skip();
                        break;
                    case '1':
                        e.preventDefault();
                        @this.set('selectedCompleteness', 'loose');
                        break;
                    case '2':
                        e.preventDefault();
                        @this.set('selectedCompleteness', 'cib');
                        break;
                    case '3':
                        e.preventDefault();
                        @this.set('selectedCompleteness', 'sealed');
                        break;
                }
            });
        </script>
    @endif
</x-filament-panels::page>
