#!/usr/bin/env python3
"""
Create Variant Sorter Interface for Game Boy Advance
Sort GBA items by variant (color/model) with easy controls
"""

import json

def create_gba_variant_sorter():
    """Create interactive HTML interface for GBA variant sorting"""

    # Load raw GBA scraped data
    with open('scraped_data_gba_raw.json', 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Get raw items
    raw_items = data['raw_items']

    # Convert to sorter format
    all_items = []
    for listing in raw_items:
        all_items.append({
            'id': listing['item_id'],
            'title': listing['title'],
            'price': listing['price'],
            'date': listing['sold_date'],
            'condition': listing['condition'],
            'url': listing['url'],
            'current_variant': '',  # No initial variant for GBA
            'assigned_variant': '',  # Will be set by user
            'status': 'pending'  # pending, keep, bundle, parts, reject
        })

    # Common GBA variants (user can add more)
    suggested_variants = [
        'standard-purple',
        'standard-black',
        'standard-glacier',
        'standard-orange',
        'standard-pink',
        'sp-platinum',
        'sp-cobalt',
        'sp-flame',
        'sp-graphite',
        'sp-pearl-blue',
        'sp-pearl-pink',
        'sp-tribal-edition',
        'sp-famicom',
        'sp-nes',
        'micro-silver',
        'micro-black',
        'micro-blue',
        'micro-pink',
        'micro-famicom'
    ]

    # Create HTML interface (same as GBC but adapted for GBA)
    html_content = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrixRetro - GBA Variant Sorter</title>
    <style>
        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}

        :root {{
            --bg-dark: #0f1419;
            --bg-card: #1a1f2e;
            --accent: #00d9ff;
            --text-light: #e4e6eb;
            --text-muted: #a0a3a8;
            --keep: #00ff88;
            --bundle: #ffaa00;
            --reject: #ff4444;
        }}

        body {{
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-dark);
            color: var(--text-light);
            line-height: 1.6;
        }}

        .header {{
            background: var(--bg-card);
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid #333;
        }}

        .header h1 {{
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--accent);
        }}

        .controls {{
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 0.5rem;
        }}

        .filter-group {{
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }}

        select, input {{
            background: var(--bg-dark);
            border: 1px solid #333;
            color: var(--text-light);
            padding: 0.5rem;
            border-radius: 4px;
        }}

        .stats {{
            color: var(--text-muted);
            font-size: 0.9rem;
        }}

        .container {{
            max-width: 1600px;
            margin: 0 auto;
            padding: 1rem;
        }}

        .item-card {{
            background: var(--bg-card);
            margin-bottom: 1px;
            padding: 1rem;
            display: grid;
            grid-template-columns: 50px 1fr 300px;
            gap: 1rem;
            align-items: start;
            transition: background-color 0.1s;
        }}

        .item-card:hover {{
            background: #242936;
        }}

        .item-card.status-keep {{ border-left: 3px solid var(--keep); }}
        .item-card.status-bundle {{ border-left: 3px solid var(--bundle); }}
        .item-card.status-reject {{ border-left: 3px solid var(--reject); }}
        .item-card.status-parts {{ border-left: 3px solid #9966ff; }}

        .item-card.active {{
            background: #2a3a4a !important;
            box-shadow: 0 0 0 2px var(--accent);
        }}

        .item-number {{
            font-size: 0.9rem;
            color: var(--text-muted);
            text-align: center;
        }}

        .item-info {{
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }}

        .item-title {{
            font-size: 1rem;
            color: var(--text-light);
            cursor: pointer;
        }}

        .item-title:hover {{
            color: var(--accent);
            text-decoration: underline;
        }}

        .item-meta {{
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }}

        .price {{
            color: var(--keep);
            font-weight: 600;
        }}

        .item-controls {{
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }}

        .variant-selector {{
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }}

        .variant-select {{
            flex: 1;
            min-width: 150px;
        }}

        .add-variant-btn {{
            background: #444;
            color: var(--text-light);
            border: none;
            padding: 0.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }}

        .add-variant-btn:hover {{
            background: #555;
        }}

        .status-buttons {{
            display: flex;
            gap: 0.5rem;
        }}

        .status-btn {{
            flex: 1;
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.1s;
        }}

        .status-btn.keep {{
            background: var(--keep);
            color: black;
        }}

        .status-btn.bundle {{
            background: var(--bundle);
            color: black;
        }}

        .status-btn.reject {{
            background: var(--reject);
            color: white;
        }}

        .status-btn.parts {{
            background: #9966ff;
            color: white;
        }}

        .status-btn:hover {{
            transform: scale(1.05);
        }}

        .status-btn.active {{
            box-shadow: 0 0 0 2px white;
        }}

        .progress {{
            background: #333;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
            margin-top: 0.5rem;
        }}

        .progress-bar {{
            background: var(--accent);
            height: 100%;
            transition: width 0.3s;
        }}

        .export-btn {{
            background: var(--accent);
            color: black;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
        }}

        .export-btn:hover {{
            background: #00b8d9;
        }}

        .keyboard-hints {{
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: var(--bg-card);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.75rem;
            border: 1px solid #333;
            max-width: 250px;
        }}

        .keyboard-hints h4 {{
            margin-bottom: 0.5rem;
            color: var(--accent);
        }}

        .keyboard-hints div {{
            margin: 0.25rem 0;
        }}

        .key {{
            background: #333;
            padding: 0.1rem 0.3rem;
            border-radius: 3px;
            font-weight: 600;
        }}

        .modal {{
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }}

        .modal.active {{
            display: flex;
        }}

        .modal-content {{
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
        }}

        .modal-content h3 {{
            margin-bottom: 1rem;
            color: var(--accent);
        }}

        .modal-content input {{
            width: 100%;
            margin: 1rem 0;
            padding: 0.75rem;
        }}

        .modal-buttons {{
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }}

        .modal-buttons button {{
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }}

        .hidden {{
            display: none !important;
        }}
    </style>
</head>
<body>
    <div class="header">
        <h1>🎮 Game Boy Advance - Variant Sorter</h1>
        <div class="controls">
            <div class="stats" id="stats">
                Total: {len(all_items)} |
                Keep: <span id="keep-count">0</span> |
                Bundle: <span id="bundle-count">0</span> |
                Parts: <span id="parts-count">0</span> |
                Reject: <span id="reject-count">0</span> |
                Progress: <span id="progress-percent">0%</span>
            </div>

            <div class="filter-group">
                <label>Status:</label>
                <select id="status-filter" onchange="applyFilters()">
                    <option value="">All items</option>
                    <option value="pending">Pending only</option>
                    <option value="keep">Keep only</option>
                    <option value="bundle">Bundle only</option>
                    <option value="parts">Parts only</option>
                    <option value="reject">Reject only</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Variant:</label>
                <select id="variant-filter" onchange="applyFilters()">
                    <option value="">All variants</option>
                </select>
            </div>

            <div class="filter-group">
                <label>Search:</label>
                <input type="text" id="search-input" placeholder="Title..." oninput="applyFilters()">
            </div>

            <button class="export-btn" onclick="saveProgress()" style="background: #666;">
                💾 Save Progress
            </button>

            <button class="export-btn" onclick="exportData()">
                📤 Export Final
            </button>
        </div>

        <div class="progress">
            <div class="progress-bar" id="progress-bar"></div>
        </div>

        <div style="padding: 0.5rem 1rem; font-size: 0.8rem; color: var(--text-muted);">
            <span id="save-status">Auto-save enabled • Last saved: <span id="last-saved">Never</span></span>
        </div>
    </div>

    <div class="container" id="items-container">
        <!-- Items will be inserted here -->
    </div>

    <div class="keyboard-hints">
        <h4>⌨️ Keyboard Shortcuts</h4>
        <div><span class="key">K</span> Keep</div>
        <div><span class="key">B</span> Bundle</div>
        <div><span class="key">P</span> Parts</div>
        <div><span class="key">R</span> Reject</div>
        <div><span class="key">1-9</span> Quick variant</div>
        <div><span class="key">Enter</span> Open eBay</div>
        <div><span class="key">↑/↓</span> Navigate</div>
        <hr style="margin: 0.5rem 0; border-color: #444;">
        <div style="margin-top: 0.5rem;">
            <button onclick="loadProgressFile()" style="background: #444; color: white; border: none; padding: 0.4rem; border-radius: 4px; cursor: pointer; width: 100%; font-size: 0.75rem; margin-bottom: 0.25rem;">
                📂 Load Progress File
            </button>
            <button onclick="clearProgress()" style="background: #662; color: white; border: none; padding: 0.4rem; border-radius: 4px; cursor: pointer; width: 100%; font-size: 0.75rem;">
                🗑️ Clear Progress
            </button>
        </div>
    </div>

    <input type="file" id="progress-file-input" accept=".json" style="display: none;" onchange="handleProgressFile(event)">

    <!-- Modal for adding new variant -->
    <div class="modal" id="add-variant-modal">
        <div class="modal-content">
            <h3>Add New Variant</h3>
            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.5rem;">
                Examples: sp-nes, micro-famicom, standard-purple, sp-tribal, etc.
            </p>
            <input type="text" id="new-variant-key" placeholder="Variant key (e.g., 'sp-tribal')">
            <input type="text" id="new-variant-name" placeholder="Display name (e.g., 'SP Tribal Edition')">
            <div class="modal-buttons">
                <button style="background: #666; color: white;" onclick="closeModal()">Cancel</button>
                <button style="background: var(--accent); color: black;" onclick="confirmAddVariant()">Add Variant</button>
            </div>
        </div>
    </div>

    <script>
        // Data
        let allItems = {json.dumps(all_items, ensure_ascii=False)};
        const suggestedVariants = {json.dumps(suggested_variants)};
        let customVariants = [];
        let currentItemIndex = 0;
        let addingVariantForItem = null;

        // Same JavaScript as GBC sorter (it's generic)
        {open('create_variant_sorter.py').read().split('// Data')[1].split('</script>')[0]}
    </script>
</body>
</html>
'''

    # Write HTML file
    with open('variant_sorter_gba.html', 'w', encoding='utf-8') as f:
        f.write(html_content)

    print("🎯 GBA Variant Sorter Created!")
    print("="*60)
    print(f"📊 Items to sort: {len(all_items)}")
    print(f"💡 Suggested variants: {len(suggested_variants)}")
    print(f"📁 File: variant_sorter_gba.html")
    print()
    print("🚀 Features:")
    print("   • Assign variant (GBA/SP/Micro + color) to each item")
    print("   • Add new variants on the fly")
    print("   • Keep/Bundle/Parts/Reject classification")
    print("   • Keyboard shortcuts (K/B/P/R + 1-9)")
    print("   • Auto-save to localStorage")
    print("   • Export sorted JSON when done")
    print()
    print("📋 Common GBA variants:")
    print("   STANDARD: purple, black, glacier, orange, pink")
    print("   SP: platinum, cobalt, flame, graphite, pearl-blue, pearl-pink")
    print("   SP LIMITED: tribal, famicom, nes")
    print("   MICRO: silver, black, blue, pink, famicom")
    print()
    print("💡 TIP: You can create ANY variant name you want!")
    print("         Just click the + button next to the dropdown")
    print()
    print("⌨️  Shortcuts: K(eep) B(undle) P(arts) R(eject) | 1-9 for quick variant | ↑/↓ navigate")
    print()
    print("🌐 Open variant_sorter_gba.html in your browser to start sorting!")

if __name__ == "__main__":
    create_gba_variant_sorter()
