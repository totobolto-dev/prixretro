#!/usr/bin/env python3
"""
Create sorting interface for current listings (uses proven variant sorter design)
"""
import json
import sys
import os
from datetime import datetime

def load_variants_from_db():
    """Load variants from database for sorting options"""
    import mysql.connector
    from dotenv import load_dotenv

    load_dotenv()

    conn = mysql.connector.connect(
        host=os.getenv('DB_HOST', 'ba2247864-001.eu.clouddb.ovh.net'),
        port=int(os.getenv('DB_PORT', '35831')),
        user=os.getenv('DB_USERNAME', 'prixretro_user'),
        password=os.getenv('DB_PASSWORD'),
        database=os.getenv('DB_DATABASE', 'prixretro')
    )

    cursor = conn.cursor(dictionary=True)
    cursor.execute("""
        SELECT v.id, v.slug, v.name, c.slug as console_slug, c.name as console_name
        FROM variants v
        JOIN consoles c ON v.console_id = c.id
        ORDER BY c.id, v.id
    """)
    variants = cursor.fetchall()
    cursor.close()
    conn.close()

    return variants

def create_sorter_html(json_file):
    """Create HTML sorting interface using proven variant sorter design"""

    # Load scraped data
    with open(json_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Load variants from DB
    variants = load_variants_from_db()

    # Build variant options by console
    variant_options_by_console = {}
    for v in variants:
        console = v['console_slug']
        if console not in variant_options_by_console:
            variant_options_by_console[console] = []
        variant_options_by_console[console].append({
            'id': v['id'],
            'slug': v['slug'],
            'name': v['name']
        })

    # Flatten all items
    all_items = []
    for console_slug, console_data in data.items():
        for listing in console_data['listings']:
            all_items.append({
                'id': listing['item_id'],
                'title': listing['title'],
                'price': listing['price'],
                'url': listing['url'],
                'image_url': listing.get('image_url', ''),
                'console_slug': console_slug,
                'console_name': console_data['console_name'],
                'assigned_variant': '',  # variant ID
                'status': 'pending'  # pending, keep, reject
            })

    variant_map_json = json.dumps(variant_options_by_console)
    items_json = json.dumps(all_items, ensure_ascii=False)

    html = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>PrixRetro - Sort Current Listings</title>
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        :root {{
            --bg-dark: #0f1419;
            --bg-card: #1a1f2e;
            --accent: #00d9ff;
            --text-light: #e4e6eb;
            --text-muted: #a0a3a8;
            --keep: #00ff88;
            --reject: #ff4444;
        }}
        body {{ font-family: -apple-system, BlinkMacSystemFont, sans-serif; background: var(--bg-dark); color: var(--text-light); line-height: 1.6; }}
        .header {{ background: var(--bg-card); padding: 1rem; position: sticky; top: 0; z-index: 100; border-bottom: 1px solid #333; }}
        .controls {{ display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 0.5rem; }}
        .filter-group {{ display: flex; gap: 0.5rem; align-items: center; }}
        select, input {{ background: var(--bg-dark); border: 1px solid #333; color: var(--text-light); padding: 0.5rem; border-radius: 4px; }}
        .stats {{ color: var(--text-muted); font-size: 0.9rem; }}
        .container {{ max-width: 1600px; margin: 0 auto; padding: 1rem; }}
        .item-card {{ background: var(--bg-card); margin-bottom: 1px; padding: 1rem; display: grid; grid-template-columns: 50px 1fr 300px; gap: 1rem; align-items: start; transition: background-color 0.1s; }}
        .item-card:hover {{ background: #242936; }}
        .item-card.status-keep {{ border-left: 3px solid var(--keep); }}
        .item-card.status-reject {{ border-left: 3px solid var(--reject); }}
        .item-card.active {{ background: #2a3a4a !important; box-shadow: 0 0 0 2px var(--accent); }}
        .item-number {{ font-size: 0.9rem; color: var(--text-muted); text-align: center; }}
        .item-info {{ display: flex; flex-direction: column; gap: 0.5rem; }}
        .item-title {{ font-size: 1rem; color: var(--text-light); cursor: pointer; }}
        .item-title:hover {{ color: var(--accent); text-decoration: underline; }}
        .item-meta {{ display: flex; gap: 1rem; font-size: 0.85rem; color: var(--text-muted); }}
        .price {{ color: var(--keep); font-weight: 600; }}
        .item-controls {{ display: flex; flex-direction: column; gap: 0.5rem; }}
        .variant-select {{ flex: 1; min-width: 150px; }}
        .status-buttons {{ display: flex; gap: 0.5rem; }}
        .status-btn {{ flex: 1; padding: 0.5rem; border: none; border-radius: 4px; font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all 0.1s; }}
        .status-btn.keep {{ background: var(--keep); color: black; }}
        .status-btn.reject {{ background: var(--reject); color: white; }}
        .status-btn:hover {{ transform: scale(1.05); }}
        .status-btn.active {{ box-shadow: 0 0 0 2px white; }}
        .progress {{ background: #333; height: 4px; border-radius: 2px; overflow: hidden; margin-top: 0.5rem; }}
        .progress-bar {{ background: var(--accent); height: 100%; transition: width 0.3s; }}
        .export-btn {{ background: var(--accent); color: black; border: none; padding: 0.5rem 1.5rem; border-radius: 4px; cursor: pointer; font-weight: 600; font-size: 1rem; }}
        .export-btn:hover {{ background: #00b8d9; }}
        .keyboard-hints {{ position: fixed; bottom: 1rem; right: 1rem; background: var(--bg-card); padding: 1rem; border-radius: 8px; font-size: 0.75rem; border: 1px solid #333; max-width: 250px; }}
        .keyboard-hints h4 {{ margin-bottom: 0.5rem; color: var(--accent); }}
        .keyboard-hints div {{ margin: 0.25rem 0; }}
        .key {{ background: #333; padding: 0.1rem 0.3rem; border-radius: 3px; font-weight: 600; }}
    </style>
</head>
<body>
    <div class="header">
        <div class="controls">
            <div class="stats" id="stats">
                Total: <span id="total">0</span> |
                Keep: <span id="keep-count">0</span> |
                Reject: <span id="reject-count">0</span> |
                Progress: <span id="progress-percent">0%</span>
            </div>
            <div class="filter-group">
                <label>Status:</label>
                <select id="status-filter" onchange="applyFilters()">
                    <option value="">All items</option>
                    <option value="pending">Pending only</option>
                    <option value="keep">Keep only</option>
                    <option value="reject">Reject only</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Console:</label>
                <select id="console-filter" onchange="applyFilters()">
                    <option value="">All consoles</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Search:</label>
                <input type="text" id="search-input" placeholder="Title..." oninput="applyFilters()">
            </div>
            <button class="export-btn" onclick="saveProgress()" style="background: #666;">💾 Save Progress</button>
            <button class="export-btn" onclick="exportData()">📤 Export Final</button>
        </div>
        <div class="progress"><div class="progress-bar" id="progress-bar"></div></div>
        <div style="padding: 0.5rem 1rem; font-size: 0.8rem; color: var(--text-muted);">
            <span id="save-status">Auto-save enabled • Last saved: <span id="last-saved">Never</span></span>
        </div>
    </div>
    <div class="container" id="items-container"></div>
    <div class="keyboard-hints">
        <h4>⌨️ Shortcuts</h4>
        <div><span class="key">K</span> Keep</div>
        <div><span class="key">R</span> Reject</div>
        <div><span class="key">1-9</span> Quick variant</div>
        <div><span class="key">Enter</span> Open eBay</div>
        <div><span class="key">↑/↓</span> Navigate</div>
    </div>
    <script>
        const variantMap = {variant_map_json};
        let allItems = {items_json};
        let currentItemIndex = 0;

        function init() {{
            loadFromLocalStorage();
            updateConsoleFilter();
            renderItems();
            updateStats();
            setupKeyboardShortcuts();
            setupAutoSave();
        }}

        function updateConsoleFilter() {{
            const filter = document.getElementById('console-filter');
            const consoles = [...new Set(allItems.map(i => i.console_slug))];
            consoles.forEach(c => {{
                const option = document.createElement('option');
                option.value = c;
                option.textContent = allItems.find(i => i.console_slug === c).console_name;
                filter.appendChild(option);
            }});
        }}

        function renderItems() {{
            const container = document.getElementById('items-container');
            container.innerHTML = '';
            allItems.forEach((item, index) => {{
                if (!isItemVisible(item)) return;
                const card = document.createElement('div');
                card.className = `item-card status-${{item.status}}`;
                card.id = `item-${{index}}`;

                const variants = variantMap[item.console_slug] || [];
                const variantOptions = variants.map(v =>
                    `<option value="${{v.id}}" ${{item.assigned_variant == v.id ? 'selected' : ''}}>${{v.name}}</option>`
                ).join('');

                card.innerHTML = `
                    <div class="item-number">#${{index + 1}}</div>
                    <div class="item-info">
                        <div class="item-title" onclick="openEbay(${{index}})">${{item.title}}</div>
                        <div class="item-meta">
                            <span class="price">${{item.price.toFixed(2)}}€</span>
                            <span>${{item.console_name}}</span>
                        </div>
                    </div>
                    <div class="item-controls">
                        <select class="variant-select" onchange="setVariant(${{index}}, this.value)">
                            <option value="">Select variant...</option>
                            ${{variantOptions}}
                        </select>
                        <div class="status-buttons">
                            <button class="status-btn keep ${{item.status === 'keep' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'keep')">Keep</button>
                            <button class="status-btn reject ${{item.status === 'reject' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'reject')">Reject</button>
                        </div>
                    </div>
                `;
                container.appendChild(card);
            }});
        }}

        function isItemVisible(item) {{
            const statusFilter = document.getElementById('status-filter').value;
            const consoleFilter = document.getElementById('console-filter').value;
            const searchText = document.getElementById('search-input').value.toLowerCase();
            if (statusFilter && item.status !== statusFilter) return false;
            if (consoleFilter && item.console_slug !== consoleFilter) return false;
            if (searchText && !item.title.toLowerCase().includes(searchText)) return false;
            return true;
        }}

        function setVariant(index, variantId) {{
            allItems[index].assigned_variant = variantId;
            updateStats();
            saveToLocalStorage();
        }}

        function setStatus(index, status) {{
            allItems[index].status = status;
            renderItems();
            updateStats();
            saveToLocalStorage();
            const nextPending = allItems.findIndex((item, i) => i > index && item.status === 'pending');
            if (nextPending !== -1) scrollToItem(nextPending);
        }}

        function openEbay(index) {{ window.open(allItems[index].url, '_blank'); }}

        function scrollToItem(index) {{
            document.querySelectorAll('.item-card').forEach(el => el.classList.remove('active'));
            const element = document.getElementById(`item-${{index}}`);
            if (element) {{
                element.scrollIntoView({{ behavior: 'smooth', block: 'center' }});
                element.classList.add('active');
                currentItemIndex = index;
            }}
        }}

        function applyFilters() {{ renderItems(); }}

        function updateStats() {{
            const keepCount = allItems.filter(i => i.status === 'keep').length;
            const rejectCount = allItems.filter(i => i.status === 'reject').length;
            const processed = keepCount + rejectCount;
            const progress = Math.round((processed / allItems.length) * 100);
            document.getElementById('total').textContent = allItems.length;
            document.getElementById('keep-count').textContent = keepCount;
            document.getElementById('reject-count').textContent = rejectCount;
            document.getElementById('progress-percent').textContent = progress + '%';
            document.getElementById('progress-bar').style.width = progress + '%';
        }}

        function setupKeyboardShortcuts() {{
            document.addEventListener('keydown', (e) => {{
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
                const visibleItems = allItems.map((item, index) => ({{ item, index }})).filter(({{ item }}) => isItemVisible(item));
                if (!visibleItems.length) return;
                const currentVisible = visibleItems.findIndex(({{ index }}) => index === currentItemIndex);

                switch(e.key.toLowerCase()) {{
                    case 'k':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) setStatus(visibleItems[currentVisible].index, 'keep');
                        break;
                    case 'r':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) setStatus(visibleItems[currentVisible].index, 'reject');
                        break;
                    case 'enter':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) openEbay(visibleItems[currentVisible].index);
                        break;
                    case 'arrowup':
                        e.preventDefault();
                        if (currentVisible > 0) scrollToItem(visibleItems[currentVisible - 1].index);
                        break;
                    case 'arrowdown':
                        e.preventDefault();
                        if (currentVisible < visibleItems.length - 1) scrollToItem(visibleItems[currentVisible + 1].index);
                        break;
                    case '1': case '2': case '3': case '4': case '5': case '6': case '7': case '8': case '9':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            const item = allItems[visibleItems[currentVisible].index];
                            const variants = variantMap[item.console_slug] || [];
                            const variantIndex = parseInt(e.key) - 1;
                            if (variantIndex < variants.length) {{
                                setVariant(visibleItems[currentVisible].index, variants[variantIndex].id);
                                const select = document.querySelector(`#item-${{visibleItems[currentVisible].index}} .variant-select`);
                                if (select) select.value = variants[variantIndex].id;
                            }}
                        }}
                        break;
                }}
            }});
        }}

        function exportData() {{
            const kept = allItems.filter(i => i.status === 'keep' && i.assigned_variant);
            if (!kept.length) {{
                alert('⚠️ No items marked as "keep" with variants assigned!');
                return;
            }}
            const blob = new Blob([JSON.stringify(kept, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sorted_current_listings_' + new Date().toISOString().split('T')[0] + '.json';
            a.click();
            URL.revokeObjectURL(url);
            alert(`✅ Exported ${{kept.length}} items`);
        }}

        function saveProgress() {{
            const data = {{ items: allItems, index: currentItemIndex, timestamp: new Date().toISOString() }};
            const blob = new Blob([JSON.stringify(data, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sorting_progress_' + new Date().toISOString().split('T')[0] + '.json';
            a.click();
            URL.revokeObjectURL(url);
            alert('✅ Progress saved to file!');
        }}

        function saveToLocalStorage() {{
            try {{
                const data = {{ items: allItems, index: currentItemIndex, timestamp: new Date().toISOString() }};
                localStorage.setItem('current_sorter_progress', JSON.stringify(data));
                document.getElementById('last-saved').textContent = new Date().toLocaleTimeString('fr-FR', {{ hour: '2-digit', minute: '2-digit' }});
            }} catch (e) {{
                console.error('Save failed:', e);
            }}
        }}

        function loadFromLocalStorage() {{
            try {{
                const saved = localStorage.getItem('current_sorter_progress');
                if (!saved) return;
                const data = JSON.parse(saved);
                const savedIds = new Set(data.items.map(i => i.id));
                const currentIds = new Set(allItems.map(i => i.id));
                const match = savedIds.size === currentIds.size && [...savedIds].every(id => currentIds.has(id));
                if (match) {{
                    allItems = data.items;
                    currentItemIndex = data.index || 0;
                    const sortedCount = allItems.filter(i => i.status !== 'pending').length;
                    document.getElementById('save-status').innerHTML = `✅ Progress restored: ${{sortedCount}} items sorted • Saved ${{new Date(data.timestamp).toLocaleString()}}`;
                }}
            }} catch (e) {{
                console.error('Load failed:', e);
            }}
        }}

        function setupAutoSave() {{
            setInterval(() => saveToLocalStorage(), 30000);
            window.addEventListener('beforeunload', () => saveToLocalStorage());
        }}

        init();
    </script>
</body>
</html>'''.replace('{variant_map_json}', variant_map_json).replace('{items_json}', items_json)

    output_file = 'sort_current_listings.html'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f"✅ Created: {output_file}")
    print(f"📊 Total items to sort: {len(all_items)}")
    print(f"\n🌐 Open {output_file} in your browser to start sorting")
    print(f"\n⌨️  Shortcuts: K(eep) R(eject) | 1-9 quick variant | ↑/↓ navigate | Enter = open eBay")

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python3 sort_current_listings.py <json_file>")
        sys.exit(1)

    json_file = sys.argv[1]
    if not os.path.exists(json_file):
        print(f"❌ File not found: {json_file}")
        sys.exit(1)

    create_sorter_html(json_file)
