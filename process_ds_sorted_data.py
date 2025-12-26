#!/usr/bin/env python3
"""
Process Nintendo DS Sorted Data from variant_sorter_ds.html

Takes exported JSON from manual sorting interface and creates:
1. scraped_data_ds.json (Nintendo DS variants with stats)
2. Updates scraped_data_multiconsole.json with Nintendo DS data

Usage: python3 process_ds_sorted_data.py sorted_ds_export.json
"""

import json
import sys
import os
from datetime import datetime
from collections import defaultdict

def calculate_stats(listings):
    """Calculate statistics for a list of listings"""
    if not listings:
        return {
            "avg_price": 0,
            "min_price": 0,
            "max_price": 0,
            "listing_count": 0,
            "total_found": 0,
            "price_history": {}
        }

    prices = [l['price'] for l in listings if l['price'] > 0]

    if not prices:
        return {
            "avg_price": 0,
            "min_price": 0,
            "max_price": 0,
            "listing_count": 0,
            "total_found": len(listings),
            "price_history": {}
        }

    # Calculate price history by month
    price_history = defaultdict(list)
    for listing in listings:
        # Extract YYYY-MM from sold_date
        date_parts = listing['sold_date'].split('-')
        if len(date_parts) >= 2:
            month_key = f"{date_parts[0]}-{date_parts[1]}"
            price_history[month_key].append(listing['price'])

    # Average prices by month
    price_history_avg = {}
    for month, month_prices in price_history.items():
        price_history_avg[month] = int(sum(month_prices) / len(month_prices))

    return {
        "avg_price": int(sum(prices) / len(prices)),
        "min_price": min(prices),
        "max_price": max(prices),
        "listing_count": len(listings),
        "total_found": len(listings),
        "price_history": price_history_avg
    }

def process_sorted_ds_data(sorted_json_path):
    """Process sorted Nintendo DS data from export JSON"""

    print("="*70)
    print("📊 PROCESSING Nintendo DS SORTED DATA")
    print("="*70)
    print()

    # Load sorted data
    if not os.path.exists(sorted_json_path):
        print(f"❌ Error: {sorted_json_path} not found!")
        print()
        print("💡 To export from variant_sorter_ds.html:")
        print("   1. Open variant_sorter_ds.html in browser")
        print("   2. Click '📤 Export Final'")
        print("   3. Save the JSON file")
        print("   4. Run: python3 process_ds_sorted_data.py <exported_file>.json")
        return False

    with open(sorted_json_path, 'r', encoding='utf-8') as f:
        sorted_data = json.load(f)

    print(f"📂 Loaded sorted data: {len(sorted_data)} items")
    print()

    # Group items by variant and status
    variants_data = defaultdict(list)
    bundles = []
    parts = []
    rejected = []

    for item in sorted_data:
        status = item.get('status', 'pending')
        variant = item.get('assigned_variant', '').strip()

        if status == 'keep' and variant:
            # Convert to scraped_data format
            listing = {
                'item_id': item['id'],
                'title': item['title'],
                'price': item['price'],
                'sold_date': item['date'],
                'condition': item.get('condition', 'Occasion'),
                'url': item['url']
            }
            variants_data[variant].append(listing)
        elif status == 'bundle':
            bundles.append(item)
        elif status == 'parts':
            parts.append(item)
        elif status == 'reject':
            rejected.append(item)

    print("📊 Sorting results:")
    print(f"   • Kept variants: {len(variants_data)}")
    print(f"   • Total consoles: {sum(len(v) for v in variants_data.values())}")
    print(f"   • Bundles: {len(bundles)}")
    print(f"   • Parts: {len(parts)}")
    print(f"   • Rejected: {len(rejected)}")
    print()

    # Load Nintendo DS config for variant names
    config_path = 'config_multiconsole.json'
    if os.path.exists(config_path):
        with open(config_path, 'r', encoding='utf-8') as f:
            config = json.load(f)
        ds_config = config['consoles']['game-boy-advance']['variants']
    else:
        print("⚠️  config_multiconsole.json not found, using basic variant names")
        ds_config = {}

    # Build Nintendo DS data structure
    ds_scraped_data = {}

    for variant_key, listings in variants_data.items():
        # Get variant name from config or use key
        variant_name = ds_config.get(variant_key, {}).get('name', variant_key.replace('-', ' ').title())
        description = ds_config.get(variant_key, {}).get('description', f"Nintendo DS {variant_name}")

        stats = calculate_stats(listings)

        ds_scraped_data[variant_key] = {
            "variant_key": variant_key,
            "variant_name": variant_name,
            "description": description,
            "stats": stats,
            "listings": sorted(listings, key=lambda x: x['sold_date'], reverse=True)
        }

        print(f"  ✅ {variant_name}: {stats['listing_count']} listings, avg {stats['avg_price']}€")

    print()

    # Save Nintendo DS-only data
    output_file = 'scraped_data_ds.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(ds_scraped_data, f, indent=2, ensure_ascii=False)
    print(f"💾 Saved Nintendo DS data to: {output_file}")

    # Update multi-console data
    multiconsole_file = 'scraped_data_multiconsole.json'
    if os.path.exists(multiconsole_file):
        with open(multiconsole_file, 'r', encoding='utf-8') as f:
            multiconsole_data = json.load(f)

        # Add Nintendo DS variants
        if 'consoles' not in multiconsole_data:
            multiconsole_data['consoles'] = {}

        if 'game-boy-advance' not in multiconsole_data['consoles']:
            multiconsole_data['consoles']['game-boy-advance'] = {"variants": {}}

        multiconsole_data['consoles']['game-boy-advance']['variants'] = ds_scraped_data

        # Update metadata
        if 'metadata' not in multiconsole_data:
            multiconsole_data['metadata'] = {}
        multiconsole_data['metadata']['last_updated'] = datetime.now().isoformat()
        multiconsole_data['metadata']['ds_added'] = datetime.now().isoformat()

        # Save updated multiconsole data
        with open(multiconsole_file, 'w', encoding='utf-8') as f:
            json.dump(multiconsole_data, f, indent=2, ensure_ascii=False)

        print(f"💾 Updated multi-console data: {multiconsole_file}")
        print()
        print("📊 Multi-console structure:")
        for console_key, console_data in multiconsole_data['consoles'].items():
            variant_count = len(console_data['variants'])
            print(f"   • {console_key}: {variant_count} variants")
    else:
        print("⚠️  scraped_data_multiconsole.json not found")
        print("   Run migrate_to_multiconsole.py first to create multi-console structure")

    # Save categorized items for reference
    categorized_file = 'ds_categorized_items.json'
    categorized = {
        'variants': {k: len(v) for k, v in variants_data.items()},
        'bundles': bundles,
        'parts': parts,
        'rejected': rejected,
        'metadata': {
            'processed_date': datetime.now().isoformat(),
            'source_file': sorted_json_path,
            'total_items': len(sorted_data),
            'kept_consoles': sum(len(v) for v in variants_data.values())
        }
    }

    with open(categorized_file, 'w', encoding='utf-8') as f:
        json.dump(categorized, f, indent=2, ensure_ascii=False)
    print(f"💾 Saved categorization details to: {categorized_file}")

    print()
    print("="*70)
    print("✅ PROCESSING COMPLETE")
    print("="*70)
    print()
    print("📋 Next steps:")
    print("   1. Review scraped_data_ds.json")
    print("   2. Run: python3 update_site_compact.py")
    print("   3. Check output/ for Nintendo DS variant pages")
    print()

    return True

if __name__ == '__main__':
    if len(sys.argv) < 2:
        print("❌ Error: Missing sorted data file")
        print()
        print("Usage: python3 process_ds_sorted_data.py <exported_json_file>")
        print()
        print("Example:")
        print("   python3 process_ds_sorted_data.py sorted_ds_final.json")
        print()
        sys.exit(1)

    sorted_file = sys.argv[1]
    process_sorted_ds_data(sorted_file)
