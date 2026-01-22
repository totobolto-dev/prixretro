@extends('layout')

@section('title')
Ma Collection | PrixRetro
@endsection

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="/">Accueil</a>
        <span>›</span>
        <span>Ma Collection</span>
    </div>

    <h1>🎮 Ma Collection</h1>

    @if(session('success'))
    <div style="padding: 1rem; margin-bottom: 1.5rem; background: #10b981; color: white; border-radius: var(--radius);">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div style="padding: 1rem; margin-bottom: 1.5rem; background: #ef4444; color: white; border-radius: var(--radius);">
        {{ session('error') }}
    </div>
    @endif

    @if($collection->count() > 0)
    {{-- Collection Summary --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Valeur totale actuelle</div>
            <div style="font-size: 2rem; font-weight: 700; color: var(--accent-primary);">{{ number_format($totalValue, 0) }}€</div>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Prix d'achat total</div>
            <div style="font-size: 2rem; font-weight: 700;">{{ number_format($totalPurchasePrice, 0) }}€</div>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Profit / Perte</div>
            <div style="font-size: 2rem; font-weight: 700; color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};">
                {{ $profitLoss >= 0 ? '+' : '' }}{{ number_format($profitLoss, 0) }}€
                <span style="font-size: 1.2rem; margin-left: 0.5rem;">{{ $profitLoss >= 0 ? '📈' : '📉' }}</span>
            </div>
        </div>

        <div style="background: var(--bg-card); padding: 1.5rem; border-radius: var(--radius); border: 1px solid var(--border-color);">
            <div style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0.5rem;">Nombre de consoles</div>
            <div style="font-size: 2rem; font-weight: 700;">{{ $collection->count() }}</div>
        </div>
    </div>

    {{-- Collection Items --}}
    <div style="background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: var(--bg-darker); border-bottom: 1px solid var(--border-color);">
                <tr>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">Console</th>
                    <th style="padding: 1rem; text-align: left; font-weight: 600;">État</th>
                    <th style="padding: 1rem; text-align: right; font-weight: 600;">Prix d'achat</th>
                    <th style="padding: 1rem; text-align: right; font-weight: 600;">Valeur actuelle</th>
                    <th style="padding: 1rem; text-align: right; font-weight: 600;">+/-</th>
                    <th style="padding: 1rem; text-align: center; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collection as $item)
                @php
                    $currentValue = $item->getCurrentValue();
                    $profitLoss = $item->getProfitLoss();
                @endphp
                <tr style="border-bottom: 1px solid var(--border-color);" id="row-{{ $item->id }}">
                    <td style="padding: 1rem;">
                        <a href="/{{ $item->variant->console->slug }}/{{ $item->variant->slug }}" style="color: var(--accent-primary); text-decoration: none; font-weight: 500;">
                            {{ $item->variant->display_name }}
                        </a>
                    </td>
                    <td style="padding: 1rem;">
                        <span class="completeness-badge" data-item-id="{{ $item->id }}" style="cursor: pointer; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 500; display: inline-block;
                            @if($item->completeness === 'loose') background: #64748b; color: white;
                            @elseif($item->completeness === 'cib') background: #3b82f6; color: white;
                            @elseif($item->completeness === 'sealed') background: #f59e0b; color: white;
                            @else background: var(--bg-darker); color: var(--text-secondary);
                            @endif">
                            @if($item->completeness === 'loose') ⚪ Loose
                            @elseif($item->completeness === 'cib') 📦 CIB
                            @elseif($item->completeness === 'sealed') 🔒 Sealed
                            @else Non renseigné
                            @endif
                        </span>
                    </td>
                    <td style="padding: 1rem; text-align: right; font-family: monospace;">
                        @if($item->purchase_price)
                            {{ number_format($item->purchase_price, 0) }}€
                        @else
                            <span style="color: var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: right; font-family: monospace; font-weight: 600;">
                        @if($currentValue)
                            {{ number_format($currentValue, 0) }}€
                        @else
                            <span style="color: var(--text-secondary);">Données insuffisantes</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: right; font-family: monospace; font-weight: 600;">
                        @if($profitLoss !== null)
                            <span style="color: {{ $profitLoss >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ $profitLoss >= 0 ? '+' : '' }}{{ number_format($profitLoss, 0) }}€
                            </span>
                        @else
                            <span style="color: var(--text-secondary);">—</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <button onclick="editItem({{ $item->id }})" style="background: var(--accent-primary); color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius); cursor: pointer; font-size: 0.9rem; margin-right: 0.5rem;">
                            ✏️ Modifier
                        </button>
                        <form method="POST" action="{{ route('collection.remove', $item) }}" style="display: inline;" onsubmit="return confirm('Supprimer cette console de votre collection ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: #ef4444; color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius); cursor: pointer; font-size: 0.9rem;">
                                🗑️
                            </button>
                        </form>
                    </td>
                </tr>
                <tr id="edit-{{ $item->id }}" style="display: none; background: var(--bg-darker);">
                    <td colspan="6" style="padding: 1.5rem;">
                        <form method="POST" action="{{ route('collection.update', $item) }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">État</label>
                                <select name="completeness" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary);">
                                    <option value="">Non renseigné</option>
                                    <option value="loose" {{ $item->completeness === 'loose' ? 'selected' : '' }}>⚪ Loose (Console seule)</option>
                                    <option value="cib" {{ $item->completeness === 'cib' ? 'selected' : '' }}>📦 CIB (Complet en boîte)</option>
                                    <option value="sealed" {{ $item->completeness === 'sealed' ? 'selected' : '' }}>🔒 Sealed (Neuf scellé)</option>
                                </select>
                            </div>

                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Prix d'achat (€)</label>
                                <input type="number" name="purchase_price" step="0.01" min="0" value="{{ $item->purchase_price }}" placeholder="Ex: 45.00" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary);">
                            </div>

                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Date d'achat</label>
                                <input type="date" name="purchase_date" value="{{ $item->purchase_date ? $item->purchase_date->format('Y-m-d') : '' }}" style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary);">
                            </div>

                            <div style="grid-column: 1 / -1;">
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Notes</label>
                                <textarea name="notes" rows="2" placeholder="Ex: État excellent, rayure légère sur l'écran..." style="width: 100%; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius); background: var(--bg-card); color: var(--text-primary);">{{ $item->notes }}</textarea>
                            </div>

                            <div style="display: flex; gap: 0.5rem;">
                                <button type="submit" style="background: #10b981; color: white; border: none; padding: 0.5rem 1rem; border-radius: var(--radius); cursor: pointer; font-weight: 500;">
                                    💾 Enregistrer
                                </button>
                                <button type="button" onclick="cancelEdit({{ $item->id }})" style="background: var(--bg-card); color: var(--text-primary); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius); cursor: pointer;">
                                    Annuler
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @else
    {{-- Empty State --}}
    <div style="text-align: center; padding: 4rem 2rem; background: var(--bg-card); border-radius: var(--radius); border: 1px solid var(--border-color);">
        <div style="font-size: 4rem; margin-bottom: 1rem;">📦</div>
        <h2 style="margin-bottom: 1rem;">Votre collection est vide</h2>
        <p style="color: var(--text-secondary); margin-bottom: 2rem;">
            Commencez à ajouter des consoles à votre collection pour suivre leur valeur et votre profit/perte.
        </p>
        <a href="/" style="display: inline-block; background: var(--accent-primary); color: white; padding: 1rem 2rem; border-radius: var(--radius); text-decoration: none; font-weight: 600;">
            Parcourir les consoles
        </a>
    </div>
    @endif
</div>

<script>
function editItem(itemId) {
    // Hide all edit forms
    document.querySelectorAll('[id^="edit-"]').forEach(row => {
        row.style.display = 'none';
    });

    // Show the edit form for this item
    document.getElementById('edit-' + itemId).style.display = 'table-row';
}

function cancelEdit(itemId) {
    document.getElementById('edit-' + itemId).style.display = 'none';
}
</script>
@endsection
