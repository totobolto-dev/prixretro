#!/usr/bin/env python3
"""
Create sorting interface for current listings
Sorts by variant and filters out bundles/parts/broken items
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
        SELECT v.id, v.slug, v.name, c.slug as console_slug
        FROM variants v
        JOIN consoles c ON v.console_id = c.id
        ORDER BY c.id, v.id
    """)
    variants = cursor.fetchall()
    cursor.close()
    conn.close()

    return variants

def create_sorter_html(json_file):
    """Create HTML sorting interface"""

    # Load scraped data
    with open(json_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Load variants from DB
    variants = load_variants_from_db()

    # Group variants by console
    variants_by_console = {}
    for v in variants:
        console = v['console_slug']
        if console not in variants_by_console:
            variants_by_console[console] = []
        variants_by_console[console].append(v)

    # Flatten all items
    all_items = []
    for console_slug, console_data in data.items():
        for listing in console_data['listings']:
            all_items.append({
                'item_id': listing['item_id'],
                'title': listing['title'],
                'price': listing['price'],
                'url': listing['url'],
                'image_url': listing.get('image_url', ''),
                'console_slug': console_slug,
                'variant_id': '',
                'status': 'pending'  # pending, keep, reject
            })

    # Build variant options HTML
    variant_options = {}
    for console_slug, variants_list in variants_by_console.items():
        options_html = '<option value="">-- Choose variant --</option>'
        for v in variants_list:
            options_html += f'<option value="{v["id"]}">{v["name"]}</option>'
        variant_options[console_slug] = options_html

    variant_options_json = json.dumps(variant_options)
    items_json = json.dumps(all_items)

    html = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Sort Current Listings - PrixRetro</title>
    <style>
        * {{ margin: 0; padding: 0; box-sizing: border-box; }}
        body {{ font-family: system-ui; background: #0a0e27; color: #e0e0e0; }}
        .header {{ background: #1a1f2e; padding: 1rem; position: sticky; top: 0; z-index: 100; border-bottom: 2px solid #00d9ff; }}
        .stats {{ display: flex; gap: 2rem; margin-top: 0.5rem; font-size: 0.9rem; }}
        .stat {{ display: flex; align-items: center; gap: 0.5rem; }}
        .badge {{ padding: 0.25rem 0.75rem; border-radius: 12px; font-weight: bold; }}
        .badge.pending {{ background: #555; }}
        .badge.keep {{ background: #00ff88; color: #000; }}
        .badge.reject {{ background: #ff4444; }}
        .container {{ padding: 2rem; max-width: 1400px; margin: 0 auto; }}
        .grid {{ display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }}
        .card {{ background: #1a1f2e; border-radius: 8px; padding: 1rem; border: 2px solid transparent; transition: all 0.2s; }}
        .card.keep {{ border-color: #00ff88; }}
        .card.reject {{ border-color: #ff4444; opacity: 0.5; }}
        .card img {{ width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 0.75rem; }}
        .card h3 {{ font-size: 0.95rem; margin-bottom: 0.5rem; color: #00d9ff; line-height: 1.4; }}
        .card .price {{ font-size: 1.25rem; font-weight: bold; color: #00ff88; margin-bottom: 0.75rem; }}
        .card .controls {{ display: flex; gap: 0.5rem; flex-direction: column; }}
        .card select {{ padding: 0.5rem; border-radius: 4px; background: #0a0e27; color: #e0e0e0; border: 1px solid #333; width: 100%; }}
        .card .actions {{ display: flex; gap: 0.5rem; }}
        .card button {{ flex: 1; padding: 0.5rem; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; transition: all 0.2s; }}
        .btn-keep {{ background: #00ff88; color: #000; }}
        .btn-keep:hover {{ background: #00cc70; }}
        .btn-reject {{ background: #ff4444; color: #fff; }}
        .btn-reject:hover {{ background: #cc3333; }}
        .btn-save {{ background: #00d9ff; color: #000; padding: 0.75rem 2rem; border: none; border-radius: 4px; font-weight: bold; font-size: 1rem; cursor: pointer; }}
        .btn-save:hover {{ background: #00b3d9; }}
        a {{ color: #00d9ff; text-decoration: none; }}
        a:hover {{ text-decoration: underline; }}
    </style>
</head>
<body>
    <div class="header">
        <h1>🎮 Sort Current Listings</h1>
        <div class="stats">
            <div class="stat">Total: <span class="badge" id="total">0</span></div>
            <div class="stat">Pending: <span class="badge pending" id="pending">0</span></div>
            <div class="stat">Keep: <span class="badge keep" id="keep">0</span></div>
            <div class="stat">Reject: <span class="badge reject" id="reject">0</span></div>
        </div>
        <button class="btn-save" onclick="saveResults()" style="margin-top: 1rem;">💾 Save Sorted Data</button>
    </div>

    <div class="container">
        <div class="grid" id="items"></div>
    </div>

    <script>
        const variantOptions = {variant_options_json};
        let items = {items_json};

        function render() {{
            const grid = document.getElementById('items');
            grid.innerHTML = items.map((item, idx) => `
                <div class="card ${{item.status}}" data-idx="${{idx}}">
                    <img src="${{item.image_url || 'https://via.placeholder.com/400x200?text=No+Image'}}" alt="Item">
                    <h3>${{item.title}}</h3>
                    <div class="price">${{item.price.toFixed(2)}} €</div>
                    <div class="controls">
                        <select onchange="setVariant(${{idx}}, this.value)">
                            ${{variantOptions[item.console_slug] || '<option>No variants</option>'}}
                        </select>
                        <div class="actions">
                            <button class="btn-keep" onclick="setStatus(${{idx}}, 'keep')">✅ Keep</button>
                            <button class="btn-reject" onclick="setStatus(${{idx}}, 'reject')">❌ Reject</button>
                        </div>
                    </div>
                    <a href="${{item.url}}" target="_blank" style="margin-top: 0.5rem; display: block; font-size: 0.85rem;">View on eBay →</a>
                </div>
            `).join('');

            updateStats();
        }}

        function setVariant(idx, variantId) {{
            items[idx].variant_id = variantId;
            localStorage.setItem('sorted_items', JSON.stringify(items));
        }}

        function setStatus(idx, status) {{
            items[idx].status = status;
            localStorage.setItem('sorted_items', JSON.stringify(items));
            render();
        }}

        function updateStats() {{
            document.getElementById('total').textContent = items.length;
            document.getElementById('pending').textContent = items.filter(i => i.status === 'pending').length;
            document.getElementById('keep').textContent = items.filter(i => i.status === 'keep').length;
            document.getElementById('reject').textContent = items.filter(i => i.status === 'reject').length;
        }}

        function saveResults() {{
            const kept = items.filter(i => i.status === 'keep' && i.variant_id);

            if (kept.length === 0) {{
                alert('⚠️ No items marked as "keep" with variants assigned!');
                return;
            }}

            const blob = new Blob([JSON.stringify(kept, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sorted_current_listings.json';
            a.click();

            alert(`✅ Saved ${{kept.length}} items to sorted_current_listings.json`);
        }}

        // Load from localStorage if available
        const saved = localStorage.getItem('sorted_items');
        if (saved) {{
            items = JSON.parse(saved);
        }}

        render();
    </script>
</body>
</html>'''.replace('{variant_options_json}', variant_options_json).replace('{items_json}', items_json)

    output_file = 'sort_current_listings.html'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f"✅ Created: {output_file}")
    print(f"📊 Total items to sort: {len(all_items)}")
    print(f"\n🌐 Open {output_file} in your browser to start sorting")

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("Usage: python3 sort_current_listings.py <json_file>")
        sys.exit(1)

    json_file = sys.argv[1]
    if not os.path.exists(json_file):
        print(f"❌ File not found: {json_file}")
        sys.exit(1)

    create_sorter_html(json_file)
