#!/usr/bin/env python3
"""
Create Variant Sorter Interface
Sort Game Boy Color items by variant (color) with easy controls
"""

import json

def create_variant_sorter():
    """Create interactive HTML interface for variant sorting"""

    # Load fresh scraped data
    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Load config to get existing variants
    with open('config.json', 'r', encoding='utf-8') as f:
        config = json.load(f)

    # Get all existing variants
    existing_variants = list(config.get('variants', {}).keys())

    # Flatten all items for review
    all_items = []
    for variant_key, variant_data in data.items():
        for listing in variant_data['listings']:
            all_items.append({
                'id': listing['item_id'],
                'title': listing['title'],
                'price': listing['price'],
                'date': listing['sold_date'],
                'condition': listing['condition'],
                'url': listing['url'],
                'current_variant': variant_key,
                'assigned_variant': '',  # Will be set by user
                'status': 'pending'  # pending, keep, bundle, reject
            })

    # Create HTML interface
    html_content = f'''<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PrixRetro - Variant Sorter</title>
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
            <input type="text" id="new-variant-key" placeholder="Variant key (e.g., 'orange', 'gold')">
            <input type="text" id="new-variant-name" placeholder="Display name (e.g., 'Orange', 'Gold Edition')">
            <div class="modal-buttons">
                <button style="background: #666; color: white;" onclick="closeModal()">Cancel</button>
                <button style="background: var(--accent); color: black;" onclick="confirmAddVariant()">Add Variant</button>
            </div>
        </div>
    </div>

    <script>
        // Data
        let allItems = {json.dumps(all_items, ensure_ascii=False)};
        const existingVariants = {json.dumps(existing_variants)};
        let customVariants = [];
        let currentItemIndex = 0;
        let addingVariantForItem = null;

        // Initialize
        function init() {{
            loadFromLocalStorage();
            updateVariantFilter();
            renderItems();
            updateStats();
            setupKeyboardShortcuts();
            setupAutoSave();
        }}

        function updateVariantFilter() {{
            const filter = document.getElementById('variant-filter');
            const allVariants = [...existingVariants, ...customVariants];
            filter.innerHTML = '<option value="">All variants</option>';
            allVariants.forEach(v => {{
                const option = document.createElement('option');
                option.value = v;
                option.textContent = v;
                filter.appendChild(option);
            }});
        }}

        function renderItems() {{
            const container = document.getElementById('items-container');
            container.innerHTML = '';

            allItems.forEach((item, index) => {{
                if (!isItemVisible(item, index)) return;

                const card = document.createElement('div');
                card.className = `item-card status-${{item.status}}`;
                card.id = `item-${{index}}`;

                const allVariants = [...existingVariants, ...customVariants];
                const variantOptions = allVariants.map(v =>
                    `<option value="${{v}}" ${{item.assigned_variant === v ? 'selected' : ''}}>${{v}}</option>`
                ).join('');

                card.innerHTML = `
                    <div class="item-number">#${{index + 1}}</div>
                    <div class="item-info">
                        <div class="item-title" onclick="openEbay(${{index}})">
                            ${{item.title}}
                        </div>
                        <div class="item-meta">
                            <span class="price">${{item.price}}€</span>
                            <span>${{item.date}}</span>
                            <span>${{item.condition}}</span>
                        </div>
                    </div>
                    <div class="item-controls">
                        <div class="variant-selector">
                            <select class="variant-select" onchange="setVariant(${{index}}, this.value)">
                                <option value="">Select variant...</option>
                                ${{variantOptions}}
                            </select>
                            <button class="add-variant-btn" onclick="showAddVariantModal(${{index}})" title="Add new variant">+</button>
                        </div>
                        <div class="status-buttons">
                            <button class="status-btn keep ${{item.status === 'keep' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'keep')">Keep</button>
                            <button class="status-btn bundle ${{item.status === 'bundle' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'bundle')">Bundle</button>
                            <button class="status-btn parts ${{item.status === 'parts' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'parts')">Parts</button>
                            <button class="status-btn reject ${{item.status === 'reject' ? 'active' : ''}}" onclick="setStatus(${{index}}, 'reject')">Reject</button>
                        </div>
                    </div>
                `;

                container.appendChild(card);
            }});
        }}

        function isItemVisible(item, index) {{
            const statusFilter = document.getElementById('status-filter').value;
            const variantFilter = document.getElementById('variant-filter').value;
            const searchText = document.getElementById('search-input').value.toLowerCase();

            if (statusFilter && item.status !== statusFilter) return false;
            if (variantFilter && item.assigned_variant !== variantFilter) return false;
            if (searchText && !item.title.toLowerCase().includes(searchText)) return false;

            return true;
        }}

        function setVariant(index, variant) {{
            allItems[index].assigned_variant = variant;
            updateStats();
            saveToLocalStorage();
        }}

        function setStatus(index, status) {{
            allItems[index].status = status;
            renderItems();
            updateStats();
            saveToLocalStorage();

            // Auto-advance to next pending item
            const nextPending = allItems.findIndex((item, i) => i > index && item.status === 'pending');
            if (nextPending !== -1) {{
                scrollToItem(nextPending);
            }}
        }}

        function showAddVariantModal(index) {{
            addingVariantForItem = index;
            document.getElementById('add-variant-modal').classList.add('active');
            document.getElementById('new-variant-key').focus();
        }}

        function closeModal() {{
            document.getElementById('add-variant-modal').classList.remove('active');
            document.getElementById('new-variant-key').value = '';
            document.getElementById('new-variant-name').value = '';
            addingVariantForItem = null;
        }}

        function confirmAddVariant() {{
            const key = document.getElementById('new-variant-key').value.trim().toLowerCase();
            const name = document.getElementById('new-variant-name').value.trim();

            if (!key || !name) {{
                alert('Please enter both variant key and name');
                return;
            }}

            if (existingVariants.includes(key) || customVariants.includes(key)) {{
                alert('This variant already exists');
                return;
            }}

            customVariants.push(key);
            updateVariantFilter();

            if (addingVariantForItem !== null) {{
                allItems[addingVariantForItem].assigned_variant = key;
                renderItems();
            }}

            closeModal();
        }}

        function openEbay(index) {{
            window.open(allItems[index].url, '_blank');
        }}

        function scrollToItem(index) {{
            // Remove active class from all items
            document.querySelectorAll('.item-card').forEach(el => el.classList.remove('active'));

            const element = document.getElementById(`item-${{index}}`);
            if (element) {{
                element.scrollIntoView({{ behavior: 'smooth', block: 'center' }});
                element.classList.add('active');
                currentItemIndex = index;
            }}
        }}

        function applyFilters() {{
            renderItems();
        }}

        function updateStats() {{
            const keepCount = allItems.filter(i => i.status === 'keep').length;
            const bundleCount = allItems.filter(i => i.status === 'bundle').length;
            const partsCount = allItems.filter(i => i.status === 'parts').length;
            const rejectCount = allItems.filter(i => i.status === 'reject').length;
            const total = allItems.length;
            const processed = keepCount + bundleCount + partsCount + rejectCount;
            const progress = Math.round((processed / total) * 100);

            document.getElementById('keep-count').textContent = keepCount;
            document.getElementById('bundle-count').textContent = bundleCount;
            document.getElementById('parts-count').textContent = partsCount;
            document.getElementById('reject-count').textContent = rejectCount;
            document.getElementById('progress-percent').textContent = progress + '%';
            document.getElementById('progress-bar').style.width = progress + '%';
        }}

        function setupKeyboardShortcuts() {{
            document.addEventListener('keydown', (e) => {{
                // Don't trigger if in input field
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;

                const visibleItems = allItems.map((item, index) => ({{ item, index }}))
                    .filter(({{ item, index }}) => isItemVisible(item, index));

                if (!visibleItems.length) return;

                const currentVisible = visibleItems.findIndex(({{ index }}) => index === currentItemIndex);

                switch(e.key.toLowerCase()) {{
                    case 'k':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            setStatus(visibleItems[currentVisible].index, 'keep');
                        }}
                        break;
                    case 'b':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            setStatus(visibleItems[currentVisible].index, 'bundle');
                        }}
                        break;
                    case 'p':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            setStatus(visibleItems[currentVisible].index, 'parts');
                        }}
                        break;
                    case 'r':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            setStatus(visibleItems[currentVisible].index, 'reject');
                        }}
                        break;
                    case 'enter':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            openEbay(visibleItems[currentVisible].index);
                        }}
                        break;
                    case 'arrowup':
                        e.preventDefault();
                        if (currentVisible > 0) {{
                            currentItemIndex = visibleItems[currentVisible - 1].index;
                            scrollToItem(currentItemIndex);
                        }}
                        break;
                    case 'arrowdown':
                        e.preventDefault();
                        if (currentVisible < visibleItems.length - 1) {{
                            currentItemIndex = visibleItems[currentVisible + 1].index;
                            scrollToItem(currentItemIndex);
                        }}
                        break;
                    case '1':
                    case '2':
                    case '3':
                    case '4':
                    case '5':
                    case '6':
                    case '7':
                    case '8':
                    case '9':
                        e.preventDefault();
                        if (visibleItems[currentVisible]) {{
                            const allVariants = [...existingVariants, ...customVariants];
                            const variantIndex = parseInt(e.key) - 1;
                            if (variantIndex < allVariants.length) {{
                                const variant = allVariants[variantIndex];
                                setVariant(visibleItems[currentVisible].index, variant);
                                // Update the select dropdown visually
                                const select = document.querySelector(`#item-${{visibleItems[currentVisible].index}} .variant-select`);
                                if (select) select.value = variant;
                            }}
                        }}
                        break;
                }}
            }});
        }}

        function exportData() {{
            const result = {{
                items: allItems,
                custom_variants: customVariants,
                summary: {{
                    total: allItems.length,
                    keep: allItems.filter(i => i.status === 'keep').length,
                    bundle: allItems.filter(i => i.status === 'bundle').length,
                    parts: allItems.filter(i => i.status === 'parts').length,
                    reject: allItems.filter(i => i.status === 'reject').length
                }}
            }};

            const blob = new Blob([JSON.stringify(result, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sorted_items_' + new Date().toISOString().split('T')[0] + '.json';
            a.click();
            URL.revokeObjectURL(url);

            alert(`Exported: ${{result.summary.keep}} keep, ${{result.summary.parts}} parts, ${{result.summary.bundle}} bundle, ${{result.summary.reject}} reject`);
        }}

        function saveProgress() {{
            const progressData = {{
                items: allItems,
                custom_variants: customVariants,
                current_index: currentItemIndex,
                timestamp: new Date().toISOString()
            }};

            const blob = new Blob([JSON.stringify(progressData, null, 2)], {{ type: 'application/json' }});
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sorting_progress_' + new Date().toISOString().split('T')[0] + '.json';
            a.click();
            URL.revokeObjectURL(url);

            alert('Progress saved to file! You can continue later by loading this file.');
        }}

        function saveToLocalStorage() {{
            try {{
                const progressData = {{
                    items: allItems,
                    custom_variants: customVariants,
                    current_index: currentItemIndex,
                    timestamp: new Date().toISOString()
                }};
                localStorage.setItem('variant_sorter_progress', JSON.stringify(progressData));
                console.log('✅ Progress saved to localStorage');
                updateLastSaved();
            }} catch (e) {{
                console.error('❌ Failed to save to localStorage:', e);
                document.getElementById('save-status').innerHTML = '❌ Save failed: ' + e.message;
            }}
        }}

        function loadFromLocalStorage() {{
            try {{
                const saved = localStorage.getItem('variant_sorter_progress');
                if (!saved) {{
                    console.log('ℹ️  No saved progress found');
                    return;
                }}

                const progressData = JSON.parse(saved);
                console.log('📂 Found saved progress:', {{
                    items: progressData.items?.length,
                    timestamp: progressData.timestamp
                }});

                // Check if the data matches current items (same item IDs)
                const savedIds = new Set(progressData.items.map(i => i.id));
                const currentIds = new Set(allItems.map(i => i.id));
                const idsMatch = savedIds.size === currentIds.size &&
                                [...savedIds].every(id => currentIds.has(id));

                console.log('🔍 ID check:', {{
                    savedCount: savedIds.size,
                    currentCount: currentIds.size,
                    match: idsMatch
                }});

                if (idsMatch) {{
                    // Merge saved progress
                    allItems = progressData.items;
                    customVariants = progressData.custom_variants || [];
                    currentItemIndex = progressData.current_index || 0;

                    const sortedCount = allItems.filter(i => i.status !== 'pending').length;
                    console.log(`✅ Loaded progress: ${{sortedCount}} items sorted`);

                    document.getElementById('save-status').innerHTML =
                        `✅ Progress restored: ${{sortedCount}} items sorted • Saved ${{new Date(progressData.timestamp).toLocaleString()}}`;
                }} else {{
                    console.warn('⚠️  Saved data has different items, starting fresh');
                    document.getElementById('save-status').innerHTML =
                        `⚠️  Old progress found but doesn't match current data`;
                }}
            }} catch (e) {{
                console.error('❌ Failed to load from localStorage:', e);
                document.getElementById('save-status').innerHTML = '❌ Load failed: ' + e.message;
            }}
        }}

        function updateLastSaved() {{
            const now = new Date();
            const timeStr = now.toLocaleTimeString('fr-FR', {{ hour: '2-digit', minute: '2-digit' }});
            document.getElementById('last-saved').textContent = timeStr;
        }}

        function setupAutoSave() {{
            // Auto-save every 30 seconds
            setInterval(() => {{
                saveToLocalStorage();
            }}, 30000);

            // Also save on page unload
            window.addEventListener('beforeunload', () => {{
                saveToLocalStorage();
            }});
        }}

        function clearProgress() {{
            if (confirm('Clear all sorting progress? This cannot be undone.')) {{
                localStorage.removeItem('variant_sorter_progress');
                location.reload();
            }}
        }}

        function loadProgressFile() {{
            document.getElementById('progress-file-input').click();
        }}

        function handleProgressFile(event) {{
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {{
                try {{
                    const progressData = JSON.parse(e.target.result);

                    // Validate data
                    if (!progressData.items || !Array.isArray(progressData.items)) {{
                        alert('Invalid progress file format');
                        return;
                    }}

                    // Check if item IDs match
                    const savedIds = new Set(progressData.items.map(i => i.id));
                    const currentIds = new Set(allItems.map(i => i.id));
                    const idsMatch = savedIds.size === currentIds.size &&
                                    [...savedIds].every(id => currentIds.has(id));

                    if (!idsMatch) {{
                        if (!confirm('Warning: This progress file has different items. Load anyway?')) {{
                            return;
                        }}
                    }}

                    // Load progress
                    allItems = progressData.items;
                    customVariants = progressData.custom_variants || [];
                    currentItemIndex = progressData.current_index || 0;

                    // Update UI
                    updateVariantFilter();
                    renderItems();
                    updateStats();
                    saveToLocalStorage();

                    alert('✅ Progress loaded successfully!');
                }} catch (e) {{
                    alert('Error loading progress file: ' + e.message);
                }}
            }};
            reader.readAsText(file);

            // Reset input so same file can be loaded again
            event.target.value = '';
        }}

        // Initialize on load
        init();
    </script>
</body>
</html>
'''

    # Write HTML file
    with open('variant_sorter.html', 'w', encoding='utf-8') as f:
        f.write(html_content)

    print("🎯 Variant Sorter Created!")
    print("="*60)
    print(f"📊 Items to sort: {len(all_items)}")
    print(f"🎨 Existing variants: {len(existing_variants)}")
    print(f"📁 File: variant_sorter.html")
    print()
    print("🚀 Features:")
    print("   • Assign variant (color) to each item")
    print("   • Add new variants on the fly")
    print("   • Keep/Bundle/Parts/Reject classification")
    print("   • Keyboard shortcuts (K/B/P/R + 1-9)")
    print("   • Persistent highlighting on current item")
    print("   • Export sorted JSON when done")
    print()
    print("📋 Decision guide:")
    print("   KEEP = Single console (+battery/+1 game OK)")
    print("   BUNDLE = Multiple consoles or many games (decide later)")
    print("   PARTS = Accessories, parts catalog (saved separately)")
    print("   REJECT = Broken, wrong item (discarded)")
    print()
    print("⌨️  Shortcuts: K(eep) B(undle) P(arts) R(eject) | 1-9 for quick variant | ↑/↓ navigate")

if __name__ == "__main__":
    create_variant_sorter()
