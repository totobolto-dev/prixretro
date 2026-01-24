<x-filament-panels::page>
    <style>
        .quick-classifier-container {
            background: #0f172a;
            color: #e2e8f0;
            margin: -2rem;
            padding: 2rem;
            min-height: calc(100vh - 4rem);
        }

        .qc-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .qc-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #e2e8f0;
        }

        .qc-counter {
            font-size: 1.25rem;
            color: #94a3b8;
        }

        .qc-done-message {
            text-align: center;
            padding: 4rem;
            font-size: 2rem;
            color: #e2e8f0;
        }

        .qc-classifier {
            background: #1e293b;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
        }

        .qc-listing-info {
            display: grid;
            grid-template-columns: 400px 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .qc-listing-image {
            width: 100%;
            height: 400px;
            object-fit: contain;
            background: #0f172a;
            border-radius: 0.5rem;
        }

        .qc-no-image {
            width: 100%;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            border-radius: 0.5rem;
            color: #475569;
            font-size: 1.2rem;
        }

        .qc-listing-details {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .qc-listing-title {
            font-size: 1.5rem;
            font-weight: 600;
            line-height: 1.4;
            color: #e2e8f0;
        }

        .qc-price {
            font-weight: 700;
            color: #22c55e;
            font-size: 2rem;
        }

        .qc-variant-name {
            color: #94a3b8;
            font-size: 1.1rem;
        }

        .qc-ebay-link {
            color: #60a5fa;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .qc-ebay-link:hover {
            text-decoration: underline;
        }

        .qc-form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #334155;
        }

        .qc-form-section:last-of-type {
            border-bottom: none;
        }

        .qc-form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .qc-select, .qc-input {
            width: 100%;
            padding: 0.75rem;
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 0.5rem;
            color: #e2e8f0;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .qc-select:focus, .qc-input:focus {
            outline: none;
            border-color: #60a5fa;
        }

        .qc-select:disabled, .qc-input:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .qc-helper {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.25rem;
        }

        .qc-completeness-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .qc-completeness-btn {
            padding: 2rem 1rem;
            background: #334155;
            border: 3px solid #334155;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #94a3b8;
        }

        .qc-completeness-btn:hover {
            background: #475569;
            border-color: #475569;
        }

        .qc-completeness-btn.active-loose {
            background: #475569;
            border-color: #64748b;
            color: #fff;
        }

        .qc-completeness-btn.active-cib {
            background: #1e40af;
            border-color: #3b82f6;
            color: #fff;
        }

        .qc-completeness-btn.active-sealed {
            background: #b45309;
            border-color: #f59e0b;
            color: #fff;
        }

        .qc-btn-emoji {
            font-size: 3rem;
        }

        .qc-btn-label {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .qc-btn-desc {
            font-size: 0.85rem;
            opacity: 0.8;
            font-weight: 400;
        }

        .qc-action-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .qc-action-btn {
            padding: 2rem;
            font-size: 1.5rem;
            font-weight: 600;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            color: #fff;
        }

        .qc-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .qc-action-btn:active {
            transform: translateY(0);
        }

        .qc-btn-approve {
            background: #22c55e;
        }

        .qc-btn-approve:hover {
            background: #16a34a;
        }

        .qc-btn-reject {
            background: #ef4444;
        }

        .qc-btn-reject:hover {
            background: #dc2626;
        }

        .qc-btn-hold {
            background: #f59e0b;
        }

        .qc-btn-hold:hover {
            background: #d97706;
        }

        .qc-btn-skip {
            background: #64748b;
        }

        .qc-btn-skip:hover {
            background: #475569;
        }

        .qc-keyboard-hint {
            text-align: center;
            margin-top: 1rem;
            color: #64748b;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .qc-listing-info {
                grid-template-columns: 1fr;
            }

            .qc-action-grid, .qc-completeness-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="quick-classifier-container">
        @if($isDone)
            <div class="qc-done-message">
                🎉 Terminé ! Toutes les annonces en attente ont été traitées.
                <br><br>
                <a href="{{ route('filament.admin.resources.listings.index') }}" class="qc-ebay-link">← Retour aux listings</a>
            </div>
        @elseif($currentListing)
            <div class="qc-header">
                <h1>⚡ Quick Classifier</h1>
                <div class="qc-counter">{{ $remainingCount }} annonces restantes</div>
            </div>

            <div class="qc-classifier">
                <div class="qc-listing-info">
                    @if($currentListing->thumbnail_url)
                        <img src="{{ $currentListing->thumbnail_url }}"
                             alt="{{ $currentListing->title }}"
                             class="qc-listing-image">
                    @else
                        <div class="qc-no-image">Pas d'image</div>
                    @endif

                    <div class="qc-listing-details">
                        <div class="qc-listing-title">{{ $currentListing->title }}</div>
                        <div class="qc-price">{{ number_format($currentListing->price, 2) }}€</div>
                        @if($currentListing->variant)
                            <div class="qc-variant-name">
                                {{ $currentListing->variant->console->name }} - {{ $currentListing->variant->name }}
                            </div>
                        @endif
                        @if($currentListing->sold_date)
                            <div class="qc-variant-name">
                                Vendu le {{ $currentListing->sold_date->format('d/m/Y') }}
                            </div>
                        @endif
                        <a href="{{ $currentListing->url }}" target="_blank" class="qc-ebay-link">Voir sur eBay →</a>
                    </div>
                </div>

                {{-- Console Selection --}}
                <div class="qc-form-section">
                    <label class="qc-form-label">Console</label>
                    <select wire:model.live="selectedConsole" class="qc-select">
                        <option value="">-- Sélectionner --</option>
                        @foreach($this->getConsoles() as $slug => $name)
                            <option value="{{ $slug }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Variant Selection --}}
                <div class="qc-form-section">
                    <label class="qc-form-label">Variante</label>
                    <select wire:model="selectedVariant" @if(!$selectedConsole) disabled @endif class="qc-select">
                        <option value="">-- Variante par défaut --</option>
                        @foreach($this->getVariants() as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    <p class="qc-helper">Laissez vide pour la variante par défaut, ou créez-en une ci-dessous</p>
                </div>

                {{-- New Variant --}}
                <div class="qc-form-section">
                    <label class="qc-form-label">Ou créer une nouvelle variante</label>
                    <input type="text"
                           wire:model="newVariantName"
                           @if(!$selectedConsole) disabled @endif
                           placeholder="Ex: Atomic Purple"
                           class="qc-input">
                </div>

                {{-- Completeness --}}
                <div class="qc-form-section">
                    <label class="qc-form-label">Complétude</label>
                    <div class="qc-completeness-grid">
                        <button type="button"
                                wire:click="$set('selectedCompleteness', 'loose')"
                                class="qc-completeness-btn {{ $selectedCompleteness === 'loose' ? 'active-loose' : '' }}">
                            <span class="qc-btn-emoji">⚪</span>
                            <span class="qc-btn-label">Loose</span>
                            <span class="qc-btn-desc">Console seule</span>
                        </button>
                        <button type="button"
                                wire:click="$set('selectedCompleteness', 'cib')"
                                class="qc-completeness-btn {{ $selectedCompleteness === 'cib' ? 'active-cib' : '' }}">
                            <span class="qc-btn-emoji">📦</span>
                            <span class="qc-btn-label">CIB</span>
                            <span class="qc-btn-desc">Complet en boîte</span>
                        </button>
                        <button type="button"
                                wire:click="$set('selectedCompleteness', 'sealed')"
                                class="qc-completeness-btn {{ $selectedCompleteness === 'sealed' ? 'active-sealed' : '' }}">
                            <span class="qc-btn-emoji">🔒</span>
                            <span class="qc-btn-label">Sealed</span>
                            <span class="qc-btn-desc">Neuf scellé</span>
                        </button>
                    </div>
                    <p class="qc-helper" style="margin-top: 0.5rem;">Loose = console seule | CIB = complet boîte | Sealed = neuf scellé</p>
                </div>

                {{-- Action Buttons --}}
                <div class="qc-action-grid">
                    <button type="button" wire:click="approve" class="qc-action-btn qc-btn-approve">
                        <div class="qc-btn-emoji">✓</div>
                        Approuver
                    </button>
                    <button type="button" wire:click="reject" class="qc-action-btn qc-btn-reject">
                        <div class="qc-btn-emoji">✗</div>
                        Rejeter
                    </button>
                    <button type="button" wire:click="hold" class="qc-action-btn qc-btn-hold">
                        <div class="qc-btn-emoji">⏸</div>
                        Hold
                    </button>
                    <button type="button" wire:click="skip" class="qc-action-btn qc-btn-skip">
                        <div class="qc-btn-emoji">→</div>
                        Skip
                    </button>
                </div>

                <div class="qc-keyboard-hint">
                    Raccourcis : A = Approuver • R = Rejeter • H = Hold • S = Skip • 1 = Loose • 2 = CIB • 3 = Sealed
                </div>
            </div>
        @endif
    </div>

    {{-- Keyboard shortcuts --}}
    <script>
        document.addEventListener('keydown', function(e) {
            // Ignore if typing in input/textarea/select
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
</x-filament-panels::page>
