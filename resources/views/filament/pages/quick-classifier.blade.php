<div>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            overflow-x: hidden;
        }

        .qc-container {
            background: #0f172a;
            color: #e2e8f0;
            padding: 1rem;
            min-height: 100vh;
        }

        .qc-header {
            text-align: center;
            margin-bottom: 1rem;
        }

        .qc-header h1 {
            font-size: 1.5rem;
            margin-bottom: 0.25rem;
        }

        .qc-counter {
            font-size: 1rem;
            color: #94a3b8;
        }

        .qc-done-message {
            text-align: center;
            padding: 3rem;
            font-size: 1.5rem;
        }

        .qc-classifier {
            background: #1e293b;
            border-radius: 0.5rem;
            padding: 1rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .qc-main-grid {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .qc-image-col {
            display: flex;
            flex-direction: column;
        }

        .qc-listing-image {
            width: 100%;
            height: 280px;
            object-fit: contain;
            background: #0f172a;
            border-radius: 0.25rem;
        }

        .qc-no-image {
            width: 100%;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            border-radius: 0.25rem;
            color: #475569;
        }

        .qc-listing-title {
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.3;
            margin-top: 0.5rem;
            color: #e2e8f0;
        }

        .qc-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .qc-price {
            font-weight: 700;
            color: #22c55e;
            font-size: 1.5rem;
        }

        .qc-date {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .qc-ebay-link {
            color: #60a5fa;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .qc-ebay-link:hover {
            text-decoration: underline;
        }

        .qc-form-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .qc-form-group {
            display: flex;
            flex-direction: column;
        }

        .qc-form-group.full {
            grid-column: 1 / -1;
        }

        .qc-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .qc-select, .qc-input {
            padding: 0.5rem;
            background: #0f172a;
            border: 2px solid #334155;
            border-radius: 0.25rem;
            color: #e2e8f0;
            font-size: 0.875rem;
            transition: border-color 0.2s;
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
            font-size: 0.65rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .qc-completeness-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }

        .qc-completeness-btn {
            padding: 1rem 0.5rem;
            background: #334155;
            border: 2px solid #334155;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: #94a3b8;
        }

        .qc-completeness-btn:hover {
            background: #475569;
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
            font-size: 2rem;
        }

        .qc-btn-label {
            font-size: 0.875rem;
            font-weight: 600;
        }

        .qc-btn-desc {
            font-size: 0.65rem;
            opacity: 0.8;
        }

        .qc-actions {
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .qc-action-btn {
            padding: 1.25rem 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            border: none;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            color: #fff;
        }

        .qc-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .qc-action-btn:active {
            transform: translateY(0);
        }

        .qc-action-btn .qc-btn-emoji {
            font-size: 1.5rem;
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
            margin-top: 0.75rem;
            color: #64748b;
            font-size: 0.75rem;
        }

        @media (max-width: 1024px) {
            .qc-main-grid {
                grid-template-columns: 1fr;
            }

            .qc-form-col {
                grid-template-columns: 1fr;
            }

            .qc-actions {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>

    <div class="qc-container">
        @if($isDone)
            <div class="qc-done-message">
                🎉 Terminé ! Toutes les annonces en attente ont été traitées.
                <br><br>
                <a href="/admin/listings" class="qc-ebay-link">← Retour aux listings</a>
            </div>
        @elseif($currentListing)
            <div class="qc-header">
                <h1>⚡ Quick Classifier</h1>
                <div class="qc-counter">{{ $remainingCount }} restantes</div>
            </div>

            <div class="qc-classifier">
                <div class="qc-main-grid">
                    {{-- Left: Image and Info --}}
                    <div class="qc-image-col">
                        @if($currentListing->thumbnail_url)
                            <img src="{{ $currentListing->thumbnail_url }}"
                                 alt="{{ $currentListing->title }}"
                                 class="qc-listing-image">
                        @else
                            <div class="qc-no-image">Pas d'image</div>
                        @endif

                        <div class="qc-listing-title">{{ $currentListing->title }}</div>
                        <div class="qc-meta">
                            <span class="qc-price">{{ number_format($currentListing->price, 2) }}€</span>
                            @if($currentListing->sold_date)
                                <span class="qc-date">{{ $currentListing->sold_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                        <a href="{{ $currentListing->url }}" target="_blank" class="qc-ebay-link">Voir sur eBay →</a>
                    </div>

                    {{-- Right: Form --}}
                    <div class="qc-form-col">
                        {{-- Console --}}
                        <div class="qc-form-group">
                            <label class="qc-label">Console</label>
                            <select wire:model.live="selectedConsole" class="qc-select">
                                <option value="">-- Sélectionner --</option>
                                @foreach($this->getConsoles() as $slug => $name)
                                    <option value="{{ $slug }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Variant --}}
                        <div class="qc-form-group">
                            <label class="qc-label">Variante</label>
                            <select wire:model="selectedVariant" @if(!$selectedConsole) disabled @endif class="qc-select">
                                <option value="">-- Défaut --</option>
                                @foreach($this->getVariants() as $id => $name)
                                    <option value="{{ $id }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- New Variant --}}
                        <div class="qc-form-group full">
                            <label class="qc-label">Nouvelle variante (optionnel)</label>
                            <input type="text"
                                   wire:model="newVariantName"
                                   @if(!$selectedConsole) disabled @endif
                                   placeholder="Ex: Atomic Purple"
                                   class="qc-input">
                        </div>

                        {{-- Completeness --}}
                        <div class="qc-form-group full">
                            <label class="qc-label">Complétude</label>
                            <div class="qc-completeness-grid">
                                <button type="button"
                                        wire:click="$set('selectedCompleteness', 'loose')"
                                        class="qc-completeness-btn {{ $selectedCompleteness === 'loose' ? 'active-loose' : '' }}">
                                    <span class="qc-btn-emoji">⚪</span>
                                    <span class="qc-btn-label">Loose</span>
                                    <span class="qc-btn-desc">Seule</span>
                                </button>
                                <button type="button"
                                        wire:click="$set('selectedCompleteness', 'cib')"
                                        class="qc-completeness-btn {{ $selectedCompleteness === 'cib' ? 'active-cib' : '' }}">
                                    <span class="qc-btn-emoji">📦</span>
                                    <span class="qc-btn-label">CIB</span>
                                    <span class="qc-btn-desc">Boîte</span>
                                </button>
                                <button type="button"
                                        wire:click="$set('selectedCompleteness', 'sealed')"
                                        class="qc-completeness-btn {{ $selectedCompleteness === 'sealed' ? 'active-sealed' : '' }}">
                                    <span class="qc-btn-emoji">🔒</span>
                                    <span class="qc-btn-label">Sealed</span>
                                    <span class="qc-btn-desc">Scellé</span>
                                </button>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="qc-actions">
                            <button type="button" wire:click="approve" class="qc-action-btn qc-btn-approve">
                                <div class="qc-btn-emoji">✓</div>
                                <span class="qc-btn-label">Approuver</span>
                            </button>
                            <button type="button" wire:click="reject" class="qc-action-btn qc-btn-reject">
                                <div class="qc-btn-emoji">✗</div>
                                <span class="qc-btn-label">Rejeter</span>
                            </button>
                            <button type="button" wire:click="hold" class="qc-action-btn qc-btn-hold">
                                <div class="qc-btn-emoji">⏸</div>
                                <span class="qc-btn-label">Hold</span>
                            </button>
                            <button type="button" wire:click="skip" class="qc-action-btn qc-btn-skip">
                                <div class="qc-btn-emoji">→</div>
                                <span class="qc-btn-label">Skip</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="qc-keyboard-hint">
                    A=Approuver • R=Rejeter • H=Hold • S=Skip • 1=Loose • 2=CIB • 3=Sealed
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('keydown', function(e) {
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
</div>
