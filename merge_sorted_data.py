#!/usr/bin/env python3
"""
Merge Sorted Data
Takes the exported sorted JSON from variant_sorter.html and updates scraped_data.json
"""

import json
import sys
from datetime import datetime

def merge_sorted_data(sorted_file='sorted_items.json'):
    """Merge user-sorted data back into scraped_data.json"""

    print("📥 Loading sorted data...")
    try:
        with open(sorted_file, 'r', encoding='utf-8') as f:
            sorted_data = json.load(f)
    except FileNotFoundError:
        print(f"❌ Error: {sorted_file} not found!")
        print("Please export your sorted data from variant_sorter.html first.")
        return

    print("📂 Loading current scraped data...")
    try:
        with open('scraped_data.json', 'r', encoding='utf-8') as f:
            current_data = json.load(f)
    except FileNotFoundError:
        print("⚠️  No existing scraped_data.json found, creating new one...")
        current_data = {}

    # Load config to ensure all variants exist
    with open('config.json', 'r', encoding='utf-8') as f:
        config = json.load(f)

    # Process sorted items
    items_by_variant = {}
    keep_count = 0
    bundle_count = 0
    parts_count = 0
    reject_count = 0

    for item in sorted_data['items']:
        status = item['status']

        if status == 'keep':
            keep_count += 1
            variant = item['assigned_variant']

            if not variant:
                print(f"⚠️  Warning: Item {item['id']} marked as KEEP but no variant assigned, skipping...")
                continue

            if variant not in items_by_variant:
                items_by_variant[variant] = []

            # Build listing object
            listing = {
                'item_id': item['id'],
                'title': item['title'],
                'price': item['price'],
                'sold_date': item['date'],
                'condition': item['condition'],
                'url': item['url']
            }
            items_by_variant[variant].append(listing)

        elif status == 'bundle':
            bundle_count += 1
            # Bundles are not added to scraped_data - user will decide later

        elif status == 'parts':
            parts_count += 1
            # Parts are saved separately for potential future use

        elif status == 'reject':
            reject_count += 1
            # Rejected items are not added

    print(f"\n📊 Sorting results:")
    print(f"   ✅ KEEP: {keep_count} items")
    print(f"   ⚠️  BUNDLE: {bundle_count} items (saved separately)")
    print(f"   🔧 PARTS: {parts_count} items (saved separately)")
    print(f"   ❌ REJECT: {reject_count} items (discarded)")

    # Add any custom variants to config
    custom_variants = sorted_data.get('custom_variants', [])
    if custom_variants:
        print(f"\n🎨 Adding {len(custom_variants)} new variants to config...")
        for variant_key in custom_variants:
            if variant_key not in config['variants']:
                display_name = variant_key.replace('-', ' ').title()
                config['variants'][variant_key] = {
                    'name': display_name,
                    'description': f"Édition {display_name}.",
                    'search_terms': [f"game boy color {variant_key.replace('-', ' ')}"],
                    'keywords': [variant_key]
                }
                print(f"   + {variant_key} ({display_name})")

        # Save updated config
        with open('config.json', 'w', encoding='utf-8') as f:
            json.dump(config, f, ensure_ascii=False, indent=2)
        print("   ✅ Config updated")

    # Build new scraped_data structure
    new_data = {}

    for variant_key, listings in items_by_variant.items():
        # Calculate stats
        prices = [l['price'] for l in listings if l['price'] > 0]

        # Use trimmed mean - exclude top 10% and bottom 10% to remove outliers
        if len(prices) >= 5:
            sorted_prices = sorted(prices)
            trim_count = max(1, len(sorted_prices) // 10)  # 10% on each side
            trimmed_prices = sorted_prices[trim_count:-trim_count]
            avg_price = round(sum(trimmed_prices) / len(trimmed_prices))
        else:
            # For small datasets, use regular mean
            avg_price = round(sum(prices) / len(prices)) if prices else 0

        min_price = min(prices) if prices else 0
        max_price = max(prices) if prices else 0

        # Calculate price history by month (YYYY-MM)
        price_by_month = {}
        for listing in listings:
            if listing['price'] > 0 and listing['sold_date']:
                # Extract YYYY-MM from sold_date (format: YYYY-MM-DD)
                month_key = listing['sold_date'][:7]  # "2024-12-22" -> "2024-12"
                if month_key not in price_by_month:
                    price_by_month[month_key] = []
                price_by_month[month_key].append(listing['price'])

        # Calculate average price per month
        price_history = {}
        for month, month_prices in price_by_month.items():
            price_history[month] = round(sum(month_prices) / len(month_prices))

        # Get variant name from config
        variant_config = config['variants'].get(variant_key, {})
        variant_name = variant_config.get('name', variant_key.replace('-', ' ').title())
        variant_description = variant_config.get('description', '')

        new_data[variant_key] = {
            'variant_key': variant_key,
            'variant_name': variant_name,
            'description': variant_description,
            'stats': {
                'avg_price': avg_price,
                'min_price': min_price,
                'max_price': max_price,
                'listing_count': len(listings),
                'total_found': len(listings),
                'price_history': price_history
            },
            'listings': sorted(listings, key=lambda x: x['sold_date'], reverse=True)
        }

    # Save bundle items separately for later review
    if bundle_count > 0:
        bundle_items = [item for item in sorted_data['items'] if item['status'] == 'bundle']
        with open('bundles_to_review.json', 'w', encoding='utf-8') as f:
            json.dump(bundle_items, f, ensure_ascii=False, indent=2)
        print(f"\n📦 {bundle_count} bundle items saved to bundles_to_review.json")

    # Save parts items separately for potential future use
    if parts_count > 0:
        parts_items = [item for item in sorted_data['items'] if item['status'] == 'parts']
        with open('parts_catalog.json', 'w', encoding='utf-8') as f:
            json.dump(parts_items, f, ensure_ascii=False, indent=2)
        print(f"🔧 {parts_count} parts items saved to parts_catalog.json")

    # Backup old scraped_data
    timestamp = datetime.now().strftime('%Y%m%d_%H%M%S')
    backup_file = f'scraped_data_backup_{timestamp}.json'
    if current_data:
        with open(backup_file, 'w', encoding='utf-8') as f:
            json.dump(current_data, f, ensure_ascii=False, indent=2)
        print(f"\n💾 Backup saved: {backup_file}")

    # Save new data
    with open('scraped_data.json', 'w', encoding='utf-8') as f:
        json.dump(new_data, f, ensure_ascii=False, indent=2)

    print(f"\n✅ scraped_data.json updated!")
    print(f"\n📋 Summary by variant:")
    for variant_key, data in sorted(new_data.items()):
        count = data['stats']['listing_count']
        avg = data['stats']['avg_price']
        print(f"   • {variant_key}: {count} items, avg {avg}€")

    print(f"\n🎯 Next steps:")
    print(f"   1. Review bundles_to_review.json if you have bundles")
    print(f"   2. Run daily: python3 scraper_ebay.py (will skip these {keep_count} items)")
    print(f"   3. Generate site: python3 update_site_compact.py")

if __name__ == "__main__":
    sorted_file = sys.argv[1] if len(sys.argv) > 1 else 'sorted_items.json'
    merge_sorted_data(sorted_file)
