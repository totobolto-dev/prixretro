#!/usr/bin/env python3
"""
Process DS Sorted Data
Converts ds_sorter.html export to format ready for Laravel import
"""

import json
import sys
from collections import defaultdict

def process_ds_sorted():
    """Process sorted DS data from ds_sorter.html export"""

    print("="*70)
    print("📊 PROCESSING DS SORTED DATA")
    print("="*70)
    print()

    # Load sorted data (exported from ds_sorter.html)
    input_file = 'downloads/ds_sorted_data.json'  # Default download location

    # Try multiple locations
    possible_locations = [
        'downloads/ds_sorted_data.json',
        'public/ds_sorted_data.json',
        'storage/app/ds_sorted_data.json',
        'ds_sorted_data.json'
    ]

    sorted_data = None
    for path in possible_locations:
        try:
            with open(path, 'r', encoding='utf-8') as f:
                sorted_data = json.load(f)
            input_file = path
            print(f"✅ Found sorted data at: {path}")
            break
        except FileNotFoundError:
            continue

    if not sorted_data:
        print("❌ Error: ds_sorted_data.json not found!")
        print()
        print("Expected locations:")
        for loc in possible_locations:
            print(f"  - {loc}")
        print()
        print("Please export JSON from ds_sorter.html and save to one of these locations.")
        return False

    # Process data - convert from console/variant structure to variant-only
    processed = defaultdict(lambda: {
        'variant_key': '',
        'variant_name': '',
        'console': '',
        'stats': {
            'avg_price': 0,
            'min_price': float('inf'),
            'max_price': 0,
            'listing_count': 0
        },
        'listings': []
    })

    total_items = 0
    console_counts = defaultdict(int)

    for key, data in sorted_data.items():
        console = data['console']
        variant = data['variant']
        items = data['items']

        # Create variant key: console/variant (e.g., "ds-lite/cobalt-blue")
        variant_key = f"{console}/{variant}"

        # Human-readable name
        console_names = {
            'ds-original': 'DS Original',
            'ds-lite': 'DS Lite',
            'dsi': 'DSi',
            'dsi-xl': 'DSi XL',
            '2ds': '2DS',
            '2ds-xl': '2DS XL',
            '3ds': '3DS',
            '3ds-xl': '3DS XL',
            'new-3ds': 'New 3DS',
            'new-3ds-xl': 'New 3DS XL',
        }

        variant_name_formatted = variant.replace('-', ' ').title()
        console_name = console_names.get(console, console.upper())
        variant_name = f"{console_name} - {variant_name_formatted}"

        # Calculate stats
        prices = [item['price'] for item in items]

        processed[variant_key] = {
            'variant_key': variant_key,
            'variant_name': variant_name,
            'console': console,
            'stats': {
                'avg_price': round(sum(prices) / len(prices), 2) if prices else 0,
                'min_price': min(prices) if prices else 0,
                'max_price': max(prices) if prices else 0,
                'listing_count': len(items)
            },
            'listings': items
        }

        total_items += len(items)
        console_counts[console] += len(items)

    # Save processed data
    output_file = 'storage/app/scraped_data_ds_processed.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(dict(processed), f, ensure_ascii=False, indent=2)

    print()
    print("="*70)
    print("✅ DS DATA PROCESSED SUCCESSFULLY!")
    print("="*70)
    print()
    print(f"📥 Input: {input_file}")
    print(f"📤 Output: {output_file}")
    print()
    print(f"📊 Statistics:")
    print(f"   Total items: {total_items}")
    print(f"   Unique variants: {len(processed)}")
    print()
    print("🎮 Items per console:")
    for console, count in sorted(console_counts.items(), key=lambda x: x[1], reverse=True):
        console_names = {
            'ds-lite': 'DS Lite',
            'dsi': 'DSi',
            '3ds-xl': '3DS XL',
            '2ds': '2DS',
        }
        name = console_names.get(console, console.upper())
        print(f"   {name:20} {count:4} items")
    print()
    print("📋 Variant breakdown:")
    for variant_key, data in sorted(processed.items(), key=lambda x: x[1]['stats']['listing_count'], reverse=True)[:10]:
        print(f"   {data['variant_name']:40} {data['stats']['listing_count']:3} items @ {data['stats']['avg_price']:6.2f}€ avg")
    print()
    print("🚀 Next steps:")
    print(f"   1. Import to Laravel: php artisan import:scraped {output_file}")
    print("   2. Review in admin panel: /admin/listings")
    print("   3. Approve/reject items")
    print("   4. Sync to production: php artisan sync:production")
    print()

    return True

if __name__ == "__main__":
    success = process_ds_sorted()
    sys.exit(0 if success else 1)
