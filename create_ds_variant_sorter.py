#!/usr/bin/env python3
"""
Create Variant Sorter Interface for Nintendo DS
Sort DS items by variant (Lite/DSi models and colors) with easy controls
"""

import json
import re

def create_ds_variant_sorter():
    """Create interactive HTML interface for DS variant sorting"""

    print("="*70)
    print("🎮 CREATING NINTENDO DS VARIANT SORTER")
    print("="*70)
    print()

    # Load raw DS scraped data
    try:
        with open('scraped_data_ds_raw.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
    except FileNotFoundError:
        print("❌ Error: scraped_data_ds_raw.json not found!")
        print()
        print("Please run: python3 scraper_ds.py")
        return False

    # Get raw items
    raw_items = data['raw_items']
    print(f"📂 Loaded {len(raw_items)} items from scraped_data_ds_raw.json")

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
            'current_variant': '',
            'assigned_variant': '',
            'status': 'pending'
        })

    # Common DS variants (from config_multiconsole.json)
    suggested_variants = [
        'lite-polar-white',
        'lite-smart-black',
        'lite-noble-pink',
        'lite-platinum',
        'lite-red',
        'lite-turquoise',
        'lite-green',
        'lite-crimson-black',
        'lite-cobalt-black',
        'dsi-black',
        'dsi-white',
        'dsi-red',
        'dsi-blue',
        'dsi-pink',
        'dsi-xl-burgundy',
        'dsi-xl-brown',
        'pokemon-dialga-palkia',
        'pokemon-black',
        'zelda-gold',
        'mario-red'
    ]

    print(f"💡 Configured {len(suggested_variants)} DS variants")
    print()

    # Read the working variant_sorter.html template
    try:
        with open('variant_sorter.html', 'r', encoding='utf-8') as f:
            html = f.read()
    except FileNotFoundError:
        print("❌ Error: variant_sorter.html template not found!")
        return False

    # Replace title
    html = html.replace(
        '<title>PrixRetro - Variant Sorter</title>',
        '<title>PrixRetro - Nintendo DS Variant Sorter</title>'
    )

    # Replace heading
    html = html.replace(
        '<h1>🎮 Game Boy Color - Variant Sorter</h1>',
        '<h1>🎮 Nintendo DS - Variant Sorter</h1>'
    )

    # Change localStorage key to avoid conflicts
    html = html.replace("'variant_sorter_progress'", "'ds_sorter_progress'")

    # Replace the allItems data using regex
    pattern1 = r'let allItems = \[{.*?}\];'
    replacement1 = 'let allItems = ' + json.dumps(all_items, ensure_ascii=False) + ';'
    html = re.sub(pattern1, replacement1, html, flags=re.DOTALL, count=1)

    # Replace existingVariants with suggestedVariants
    pattern2 = r'const existingVariants = \[.*?\];'
    replacement2 = 'const suggestedVariants = ' + json.dumps(suggested_variants) + ';'
    html = re.sub(pattern2, replacement2, html, count=1)

    # Replace all references to existingVariants
    html = html.replace('...existingVariants', '...suggestedVariants')
    html = html.replace('existingVariants', 'suggestedVariants')

    # Write DS sorter HTML
    output_file = 'variant_sorter_ds.html'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)

    print("="*70)
    print("✅ NINTENDO DS VARIANT SORTER CREATED!")
    print("="*70)
    print()
    print(f"📊 Items to sort: {len(all_items)}")
    print(f"💡 Suggested variants: {len(suggested_variants)}")
    print(f"📁 File: {output_file}")
    print()
    print("🚀 Features:")
    print("   • Assign variant (DS Lite/DSi + color) to each item")
    print("   • Add new variants on the fly")
    print("   • Keep/Bundle/Parts/Reject classification")
    print("   • Keyboard shortcuts (K/B/P/R + 1-9)")
    print("   • Auto-save to localStorage (key: ds_sorter_progress)")
    print("   • Export sorted JSON when done")
    print()
    print("📋 Common DS variants:")
    print("   DS LITE: polar-white, smart-black, noble-pink, platinum")
    print("   DS LITE COLORS: red, turquoise, green, crimson-black, cobalt-black")
    print("   DSi: black, white, red, blue, pink")
    print("   DSi XL: burgundy, brown")
    print("   LIMITED: pokemon, zelda, mario editions")
    print()
    print("💡 TIP: DS Lite was most popular, expect ~70% of items to be Lite")
    print("         DSi has cameras, DS Lite doesn't - check titles carefully")
    print()
    print("⌨️  Shortcuts: K(eep) B(undle) P(arts) R(eject) | 1-9 for quick variant | ↑/↓ navigate")
    print()
    print(f"🌐 Open {output_file} in your browser to start sorting!")
    print()

    return True

if __name__ == "__main__":
    success = create_ds_variant_sorter()
    if not success:
        print()
        print("❌ Failed to create DS variant sorter")
        print()
