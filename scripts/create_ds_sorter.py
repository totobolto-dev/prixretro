#!/usr/bin/env python3
"""
Create DS Console & Variant Sorter Interface
Two-step sorting: Console type (DS/DSi/2DS/3DS) + Variant (color/edition)
"""

import json
import sys

def create_ds_sorter():
    """Create interactive HTML interface for DS console and variant sorting"""

    print("="*70)
    print("🎮 CREATING DS CONSOLE & VARIANT SORTER")
    print("="*70)
    print()

    # Load DS data
    data_path = 'storage/app/scraped_data_ds.json'
    try:
        with open(data_path, 'r', encoding='utf-8') as f:
            data = json.load(f)
    except FileNotFoundError:
        print(f"❌ Error: {data_path} not found!")
        return False

    print(f"📂 Loaded {len(data)} items from {data_path}")

    # Convert to sorter format
    all_items = []
    for item in data:
        all_items.append({
            'id': item['item_id'],
            'title': item['title'],
            'price': item['price'],
            'date': item.get('sold_date', ''),
            'condition': item.get('condition', ''),
            'url': item['url'],
            'console': '',  # NEW: console type (ds-lite, dsi, 2ds, 3ds, etc.)
            'variant': '',  # variant within that console (color/edition)
            'status': 'pending'
        })

    # Console types to sort into
    console_types = [
        {'value': 'ds-original', 'label': 'DS Original (Fat)'},
        {'value': 'ds-lite', 'label': 'DS Lite'},
        {'value': 'dsi', 'label': 'DSi'},
        {'value': 'dsi-xl', 'label': 'DSi XL'},
        {'value': '2ds', 'label': '2DS'},
        {'value': '2ds-xl', 'label': '2DS XL (New 2DS XL)'},
        {'value': '3ds', 'label': '3DS'},
        {'value': '3ds-xl', 'label': '3DS XL'},
        {'value': 'new-3ds', 'label': 'New 3DS'},
        {'value': 'new-3ds-xl', 'label': 'New 3DS XL'},
        {'value': 'other', 'label': '❌ Other Nintendo (SNES/Switch/etc)'},
        {'value': 'unknown', 'label': '⚠️ Unknown / Review'},
    ]

    # Common variants per console (user can add more)
    suggested_variants = {
        'ds-lite': ['polar-white', 'jet-black', 'noble-pink', 'red', 'turquoise', 'green', 'cobalt-blue', 'crimson-red'],
        'dsi': ['white', 'black', 'blue', 'pink', 'red', 'lime-green'],
        'dsi-xl': ['burgundy', 'bronze', 'dark-brown', 'white', 'blue', 'yellow'],
        '2ds': ['white-red', 'black-blue', 'pink-white', 'purple-silver', 'pikachu-yellow'],
        '3ds': ['aqua-blue', 'cosmo-black', 'flame-red', 'pearl-pink', 'cobalt-blue'],
        '3ds-xl': ['red-black', 'blue-black', 'pink-white', 'silver-black'],
        'new-3ds-xl': ['metallic-black', 'metallic-blue', 'pearl-white', 'snes-edition', 'galaxy-edition'],
    }

    # Create HTML
    html = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DS Console & Variant Sorter</title>
    <style>
        * {{
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }}

        body {{
            font-family: -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0f1419;
            color: #e4e6eb;
            line-height: 1.6;
            padding-bottom: 100px;
        }}

        .header {{
            background: #1a1f2e;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 2px solid #333;
        }}

        h1 {{
            color: #00d9ff;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }}

        .stats {{
            color: #a0a3a8;
            font-size: 0.9rem;
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }}

        .stat {{
            display: flex;
            gap: 0.5rem;
        }}

        .filters {{
            margin-top: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }}

        select, input, button {{
            background: #0f1419;
            border: 1px solid #333;
            color: #e4e6eb;
            padding: 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }}

        button {{
            cursor: pointer;
            background: #00d9ff;
            color: black;
            font-weight: 600;
            border: none;
        }}

        button:hover {{
            background: #00b8d4;
        }}

        .container {{
            max-width: 1200px;
            margin: 0 auto;
            padding: 1rem;
        }}

        .item-card {{
            background: #1a1f2e;
            margin-bottom: 1px;
            padding: 1rem;
            transition: background 0.1s;
        }}

        .item-card:hover {{
            background: #242936;
        }}

        .item-card.active {{
            background: #2a3a4a !important;
            box-shadow: 0 0 0 2px #00d9ff;
        }}

        .item-card.status-keep {{ border-left: 3px solid #00ff88; }}
        .item-card.status-reject {{ border-left: 3px solid #ff4444; }}

        .item-title {{
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
            cursor: pointer;
        }}

        .item-title:hover {{
            color: #00d9ff;
        }}

        .item-meta {{
            display: flex;
            gap: 1rem;
            font-size: 0.85rem;
            color: #a0a3a8;
            margin-bottom: 1rem;
        }}

        .price {{
            color: #00ff88;
            font-weight: 600;
        }}

        .item-controls {{
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }}

        @media (max-width: 768px) {{
            .item-controls {{
                grid-template-columns: 1fr;
            }}
        }}

        .control-row {{
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }}

        .control-row label {{
            font-size: 0.8rem;
            color: #a0a3a8;
            min-width: 60px;
        }}

        .control-row select {{
            flex: 1;
        }}

        .status-buttons {{
            grid-column: 1 / -1;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }}

        .status-btn {{
            padding: 0.5rem;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }}

        .status-btn.keep {{ background: #00ff88; color: black; }}
        .status-btn.reject {{ background: #ff4444; color: white; }}

        .export-panel {{
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #1a1f2e;
            border-top: 2px solid #00d9ff;
            padding: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }}

        .hidden {{
            display: none;
        }}
    </style>
</head>
<body>
    <div class="header">
        <h1>🎮 DS Family - Console & Variant Sorter</h1>
        <div class="stats">
            <div class="stat">
                <span>Total:</span>
                <strong id="totalCount">{len(all_items)}</strong>
            </div>
            <div class="stat">
                <span>Keep:</span>
                <strong id="keepCount">0</strong>
            </div>
            <div class="stat">
                <span>Reject:</span>
                <strong id="rejectCount">0</strong>
            </div>
            <div class="stat">
                <span>Pending:</span>
                <strong id="pendingCount">{len(all_items)}</strong>
            </div>
        </div>
        <div class="filters">
            <select id="filterStatus">
                <option value="all">All Status</option>
                <option value="pending" selected>Pending Only</option>
                <option value="keep">Keep</option>
                <option value="reject">Reject</option>
            </select>
            <select id="filterConsole">
                <option value="all">All Consoles</option>
                <option value="">Not Assigned</option>
                {''.join(f'<option value="{c["value"]}">{c["label"]}</option>' for c in console_types)}
            </select>
            <input type="text" id="searchBox" placeholder="Search title...">
        </div>
    </div>

    <div class="container" id="itemsContainer"></div>

    <div class="export-panel">
        <button onclick="saveProgress()">💾 Save Progress</button>
        <button onclick="exportData()">📥 Export JSON</button>
    </div>

    <script>
        const allItems = {json.dumps(all_items, ensure_ascii=False)};
        const consoleTypes = {json.dumps(console_types)};
        const suggestedVariants = {json.dumps(suggested_variants)};

        let currentIndex = 0;
        const STORAGE_KEY = 'ds_sorter_progress';

        // Load progress
        function loadProgress() {{
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {{
                const savedData = JSON.parse(saved);
                savedData.forEach((savedItem, idx) => {{
                    if (allItems[idx] && allItems[idx].id === savedItem.id) {{
                        allItems[idx] = savedItem;
                    }}
                }});
                console.log('✅ Progress loaded');
            }}
        }}

        function saveProgress() {{
            localStorage.setItem(STORAGE_KEY, JSON.stringify(allItems));
            alert('✅ Progress saved!');
        }}

        function updateStats() {{
            const keep = allItems.filter(i => i.status === 'keep').length;
            const reject = allItems.filter(i => i.status === 'reject').length;
            const pending = allItems.filter(i => i.status === 'pending').length;

            document.getElementById('keepCount').textContent = keep;
            document.getElementById('rejectCount').textContent = reject;
            document.getElementById('pendingCount').textContent = pending;
        }}

        function renderItems() {{
            const container = document.getElementById('itemsContainer');
            const filterStatus = document.getElementById('filterStatus').value;
            const filterConsole = document.getElementById('filterConsole').value;
            const searchTerm = document.getElementById('searchBox').value.toLowerCase();

            let filtered = allItems.filter(item => {{
                if (filterStatus !== 'all' && item.status !== filterStatus) return false;
                if (filterConsole !== 'all' && item.console !== filterConsole) return false;
                if (searchTerm && !item.title.toLowerCase().includes(searchTerm)) return false;
                return true;
            }});

            container.innerHTML = filtered.map((item, idx) => {{
                const consoleOptions = consoleTypes.map(c =>
                    `<option value="${{c.value}}" ${{item.console === c.value ? 'selected' : ''}}>${{c.label}}</option>`
                ).join('');

                const variants = suggestedVariants[item.console] || [];
                const variantOptions = variants.map(v =>
                    `<option value="${{v}}" ${{item.variant === v ? 'selected' : ''}}>${{v}}</option>`
                ).join('');

                return `
                <div class="item-card status-${{item.status}}" data-id="${{item.id}}">
                    <div class="item-title" onclick="window.open('${{item.url}}', '_blank')">
                        ${{item.title}}
                    </div>
                    <div class="item-meta">
                        <span class="price">${{item.price}}€</span>
                        <span>${{item.date}}</span>
                        <span>${{item.condition}}</span>
                    </div>
                    <div class="item-controls">
                        <div class="control-row">
                            <label>Console:</label>
                            <select onchange="updateItem('${{item.id}}', 'console', this.value)">
                                <option value="">Select...</option>
                                ${{consoleOptions}}
                            </select>
                        </div>
                        <div class="control-row">
                            <label>Variant:</label>
                            <select onchange="updateItem('${{item.id}}', 'variant', this.value)">
                                <option value="">Select...</option>
                                ${{variantOptions}}
                                <option value="__new__">+ Add New</option>
                            </select>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn keep" onclick="updateItem('${{item.id}}', 'status', 'keep')">✓ Keep</button>
                            <button class="status-btn reject" onclick="updateItem('${{item.id}}', 'status', 'reject')">✗ Reject</button>
                        </div>
                    </div>
                </div>
                `;
            }}).join('');

            updateStats();
        }}

        function updateItem(id, field, value) {{
            const item = allItems.find(i => i.id === id);
            if (!item) return;

            if (field === 'variant' && value === '__new__') {{
                const newVariant = prompt('Enter new variant name (e.g., "cobalt-blue"):');
                if (newVariant) {{
                    if (!suggestedVariants[item.console]) {{
                        suggestedVariants[item.console] = [];
                    }}
                    if (!suggestedVariants[item.console].includes(newVariant)) {{
                        suggestedVariants[item.console].push(newVariant);
                    }}
                    item.variant = newVariant;
                }}
            }} else {{
                item[field] = value;

                // If console changed, update variant options
                if (field === 'console') {{
                    item.variant = '';
                }}
            }}

            saveProgress();
            renderItems();
        }}

        function exportData() {{
            // Group by console and variant
            const grouped = {{}};

            allItems.filter(i => i.status === 'keep' && i.console && i.variant).forEach(item => {{
                const key = `${{item.console}}/${{item.variant}}`;
                if (!grouped[key]) {{
                    grouped[key] = {{
                        console: item.console,
                        variant: item.variant,
                        items: []
                    }};
                }}
                grouped[key].items.push({{
                    item_id: item.id,
                    title: item.title,
                    price: item.price,
                    sold_date: item.date,
                    condition: item.condition,
                    url: item.url
                }});
            }});

            const blob = new Blob([JSON.stringify(grouped, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'ds_sorted_data.json';
            a.click();
        }}

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {{
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;

            if (e.key === 's' && e.ctrlKey) {{
                e.preventDefault();
                saveProgress();
            }}
        }});

        // Event listeners
        document.getElementById('filterStatus').addEventListener('change', renderItems);
        document.getElementById('filterConsole').addEventListener('change', renderItems);
        document.getElementById('searchBox').addEventListener('input', renderItems);

        // Initialize
        loadProgress();
        renderItems();
    </script>
</body>
</html>
'''

    # Write HTML file
    output_file = 'public/ds_sorter.html'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)

    print("="*70)
    print("✅ DS CONSOLE & VARIANT SORTER CREATED!")
    print("="*70)
    print()
    print(f"📊 Items to sort: {len(all_items)}")
    print(f"🎮 Console types: {len(console_types)}")
    print(f"📁 File: {output_file}")
    print()
    print("🚀 Features:")
    print("   • Two-step sorting: Console type + Variant")
    print("   • Filter by status/console/search")
    print("   • Auto-save to localStorage")
    print("   • Mobile-friendly interface")
    print("   • Export sorted JSON when done")
    print()
    print("📋 Workflow:")
    print("   1. Open in browser: https://www.prixretro.com/ds_sorter.html")
    print("   2. For each item: Select console type, then variant")
    print("   3. Click Keep or Reject")
    print("   4. Progress auto-saves to browser")
    print("   5. Export JSON when complete")
    print()
    print("💡 Access from mobile: Just visit the URL above!")
    print()

    return True

if __name__ == "__main__":
    success = create_ds_sorter()
    sys.exit(0 if success else 1)
