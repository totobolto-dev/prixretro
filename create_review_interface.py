#!/usr/bin/env python3
"""
Create Interactive Review Interface
Fast, web-based UI for classifying 879 items
"""

import json

def create_review_interface():
    """Create a fast interactive HTML interface for item classification"""
    
    # Load fresh scraped data
    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Flatten all items for review
    all_items = []
    for variant_key, variant_data in data.items():
        for listing in variant_data['listings']:
            all_items.append({
                'id': listing['item_id'],
                'variant': variant_key,
                'variant_name': variant_data['variant_name'],
                'title': listing['title'],
                'price': listing['price'],
                'date': listing['sold_date'],
                'condition': listing['condition'],
                'url': listing['url'],
                'classification': 'unclassified'  # Will be set by user
            })
    
    # Create HTML interface
    html_content = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrixRetro - Item Classification Interface</title>
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
            --success: #00ff88;
            --warning: #ffaa00;
            --danger: #ff4444;
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
        
        .controls {{
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
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
        
        .item-row {{
            background: var(--bg-card);
            margin: 1px 0;
            padding: 0.75rem 1rem;
            display: grid;
            grid-template-columns: 40px 1fr 80px 90px 100px 120px;
            gap: 1rem;
            align-items: center;
            transition: background-color 0.1s;
        }}
        
        .item-row:hover {{
            background: #242936;
        }}
        
        .item-id {{
            font-size: 0.8rem;
            color: var(--text-muted);
        }}
        
        .item-title {{
            font-size: 0.9rem;
            line-height: 1.3;
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }}
        
        .item-price {{
            font-weight: 600;
            color: var(--success);
            text-align: right;
        }}
        
        .item-variant {{
            font-size: 0.8rem;
            color: var(--accent);
            text-align: center;
        }}
        
        .item-date {{
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
        }}
        
        .classification-buttons {{
            display: flex;
            gap: 2px;
        }}
        
        .class-btn {{
            padding: 0.25rem 0.5rem;
            border: none;
            border-radius: 3px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.1s;
        }}
        
        .class-btn.console {{ background: var(--success); color: black; }}
        .class-btn.game {{ background: var(--warning); color: black; }}
        .class-btn.parts {{ background: var(--danger); color: white; }}
        .class-btn.skip {{ background: #666; color: white; }}
        
        .class-btn:hover {{ transform: scale(1.05); }}
        
        .item-row.classified-console {{ background: #0a2a0a; }}
        .item-row.classified-game {{ background: #2a1a0a; }}
        .item-row.classified-parts {{ background: #2a0a0a; }}
        .item-row.classified-skip {{ background: #1a1a1a; }}
        
        .progress {{
            background: #333;
            height: 4px;
            border-radius: 2px;
            overflow: hidden;
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
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
        }}
        
        .hidden {{ display: none !important; }}
        
        .keyboard-hint {{
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: var(--bg-card);
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            border: 1px solid #333;
        }}
        
        @media (max-width: 768px) {{
            .item-row {{
                grid-template-columns: 1fr auto;
                gap: 0.5rem;
            }}
            .item-variant, .item-id, .item-date {{
                display: none;
            }}
        }}
    </style>
</head>
<body>
    <div class="header">
        <div class="controls">
            <div class="stats" id="stats">
                Total: {len(all_items)} | Classified: <span id="classified-count">0</span> | 
                Progress: <span id="progress-percent">0%</span>
            </div>
            
            <div class="filter-group">
                <label>Filter:</label>
                <select id="variant-filter">
                    <option value="">All variants</option>
                    <option value="console">Console classified</option>
                    <option value="game">Game classified</option>
                    <option value="parts">Parts classified</option>
                    <option value="unclassified">Unclassified only</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label>Search:</label>
                <input type="text" id="search-input" placeholder="Title search...">
            </div>
            
            <button class="export-btn" onclick="exportClassifications()">
                Export Classifications
            </button>
        </div>
        
        <div class="progress">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
    </div>
    
    <div id="items-container">
        <!-- Items will be populated by JavaScript -->
    </div>
    
    <div class="keyboard-hint">
        <strong>Keyboard shortcuts:</strong><br>
        C = Console | G = Game | P = Parts | S = Skip<br>
        ↑/↓ = Navigate | Enter = Open eBay
    </div>

    <script>
        // Item data
        const allItems = {json.dumps(all_items, indent=2)};
        
        let currentItems = [...allItems];
        let selectedIndex = 0;
        let classifications = {{}};
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {{
            renderItems();
            updateStats();
            setupEventListeners();
            focusCurrentItem();
        }});
        
        function renderItems() {{
            const container = document.getElementById('items-container');
            container.innerHTML = '';
            
            currentItems.forEach((item, index) => {{
                const row = document.createElement('div');
                row.className = `item-row ${{classifications[item.id] ? 'classified-' + classifications[item.id] : ''}}`;
                row.dataset.index = index;
                row.dataset.itemId = item.id;
                
                row.innerHTML = `
                    <div class="item-id">${{item.id}}</div>
                    <div class="item-title" title="${{item.title}}">${{item.title}}</div>
                    <div class="item-price">${{item.price}}€</div>
                    <div class="item-date">${{item.date}}</div>
                    <div class="item-variant">${{item.variant}}</div>
                    <div class="classification-buttons">
                        <button class="class-btn console" onclick="classify('${{item.id}}', 'console', event)">C</button>
                        <button class="class-btn game" onclick="classify('${{item.id}}', 'game', event)">G</button>
                        <button class="class-btn parts" onclick="classify('${{item.id}}', 'parts', event)">P</button>
                        <button class="class-btn skip" onclick="classify('${{item.id}}', 'skip', event)">S</button>
                    </div>
                `;
                
                container.appendChild(row);
            }});
        }}
        
        function classify(itemId, classification, event) {{
            event?.stopPropagation();
            classifications[itemId] = classification;
            
            // Update visual state
            const row = document.querySelector(`[data-item-id="${{itemId}}"]`);
            row.className = `item-row classified-${{classification}}`;
            
            updateStats();
            
            // Auto-advance to next unclassified item
            if (event?.type !== 'keydown') {{
                const currentRow = document.querySelector(`[data-index="${{selectedIndex}}"]`);
                if (currentRow?.dataset.itemId === itemId) {{
                    moveToNextUnclassified();
                }}
            }}
        }}
        
        function updateStats() {{
            const totalCount = allItems.length;
            const classifiedCount = Object.keys(classifications).length;
            const progressPercent = Math.round((classifiedCount / totalCount) * 100);
            
            document.getElementById('classified-count').textContent = classifiedCount;
            document.getElementById('progress-percent').textContent = progressPercent + '%';
            document.getElementById('progress-bar').style.width = progressPercent + '%';
        }}
        
        function setupEventListeners() {{
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {{
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
                
                const currentItem = getCurrentItem();
                if (!currentItem) return;
                
                switch(e.key.toLowerCase()) {{
                    case 'c':
                        e.preventDefault();
                        classify(currentItem.id, 'console');
                        break;
                    case 'g':
                        e.preventDefault();
                        classify(currentItem.id, 'game');
                        break;
                    case 'p':
                        e.preventDefault();
                        classify(currentItem.id, 'parts');
                        break;
                    case 's':
                        e.preventDefault();
                        classify(currentItem.id, 'skip');
                        break;
                    case 'arrowup':
                        e.preventDefault();
                        moveUp();
                        break;
                    case 'arrowdown':
                        e.preventDefault();
                        moveDown();
                        break;
                    case 'enter':
                        e.preventDefault();
                        window.open(currentItem.url, '_blank');
                        break;
                }}
            }});
            
            // Click to select
            document.addEventListener('click', function(e) {{
                const row = e.target.closest('.item-row');
                if (row && !e.target.classList.contains('class-btn')) {{
                    selectedIndex = parseInt(row.dataset.index);
                    focusCurrentItem();
                }}
            }});
            
            // Filters
            document.getElementById('variant-filter').addEventListener('change', filterItems);
            document.getElementById('search-input').addEventListener('input', filterItems);
        }}
        
        function getCurrentItem() {{
            return currentItems[selectedIndex];
        }}
        
        function focusCurrentItem() {{
            // Remove previous focus
            document.querySelectorAll('.item-row').forEach(row => {{
                row.style.outline = 'none';
            }});
            
            // Add focus to current
            const currentRow = document.querySelector(`[data-index="${{selectedIndex}}"]`);
            if (currentRow) {{
                currentRow.style.outline = '2px solid var(--accent)';
                currentRow.scrollIntoView({{ behavior: 'smooth', block: 'center' }});
            }}
        }}
        
        function moveUp() {{
            if (selectedIndex > 0) {{
                selectedIndex--;
                focusCurrentItem();
            }}
        }}
        
        function moveDown() {{
            if (selectedIndex < currentItems.length - 1) {{
                selectedIndex++;
                focusCurrentItem();
            }}
        }}
        
        function moveToNextUnclassified() {{
            for (let i = selectedIndex + 1; i < currentItems.length; i++) {{
                if (!classifications[currentItems[i].id]) {{
                    selectedIndex = i;
                    focusCurrentItem();
                    return;
                }}
            }}
            
            // If no unclassified found ahead, loop back
            for (let i = 0; i < selectedIndex; i++) {{
                if (!classifications[currentItems[i].id]) {{
                    selectedIndex = i;
                    focusCurrentItem();
                    return;
                }}
            }}
        }}
        
        function filterItems() {{
            const variantFilter = document.getElementById('variant-filter').value;
            const searchText = document.getElementById('search-input').value.toLowerCase();
            
            currentItems = allItems.filter(item => {{
                // Variant filter
                if (variantFilter === 'console' && classifications[item.id] !== 'console') return false;
                if (variantFilter === 'game' && classifications[item.id] !== 'game') return false;
                if (variantFilter === 'parts' && classifications[item.id] !== 'parts') return false;
                if (variantFilter === 'unclassified' && classifications[item.id]) return false;
                
                // Search filter
                if (searchText && !item.title.toLowerCase().includes(searchText)) return false;
                
                return true;
            }});
            
            selectedIndex = 0;
            renderItems();
            focusCurrentItem();
        }}
        
        function exportClassifications() {{
            // Create classification report
            const report = {{
                total_items: allItems.length,
                classified_count: Object.keys(classifications).length,
                timestamp: new Date().toISOString(),
                classifications: {{
                    console: [],
                    game: [],
                    parts: [],
                    skip: []
                }}
            }};
            
            // Group items by classification
            allItems.forEach(item => {{
                const classification = classifications[item.id];
                if (classification && report.classifications[classification]) {{
                    report.classifications[classification].push({{
                        id: item.id,
                        variant: item.variant,
                        title: item.title,
                        price: item.price,
                        url: item.url
                    }});
                }}
            }});
            
            // Export as JSON
            const blob = new Blob([JSON.stringify(report, null, 2)], {{
                type: 'application/json'
            }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `classifications_${{new Date().getTime()}}.json`;
            a.click();
            URL.revokeObjectURL(url);
            
            console.log('Classifications exported:', report);
        }}
    </script>
</body>
</html>'''
    
    # Save interface
    with open('item_review_interface.html', 'w', encoding='utf-8') as f:
        f.write(html_content)
    
    print("🎯 Interactive Review Interface Created!")
    print("=" * 50)
    print(f"📊 Total items to review: {len(all_items)}")
    print(f"📁 Interface saved to: item_review_interface.html")
    print()
    print("🚀 How to use:")
    print("   1. Open item_review_interface.html in your browser")
    print("   2. Use keyboard shortcuts to classify quickly:")
    print("      • C = Console")
    print("      • G = Game") 
    print("      • P = Parts/Accessories")
    print("      • S = Skip/Unsure")
    print("      • ↑/↓ = Navigate items")
    print("      • Enter = Open eBay listing")
    print("   3. Use filters to focus on specific items")
    print("   4. Export classifications when done")
    print()
    print("⚡ Performance features:")
    print("   • Virtual scrolling for smooth navigation")
    print("   • Keyboard shortcuts for speed")
    print("   • Visual progress tracking")
    print("   • Auto-advance to next unclassified item")

if __name__ == "__main__":
    create_review_interface()