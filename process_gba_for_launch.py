#!/usr/bin/env python3
"""
Process kept GBA items with correct dates and prepare for launch.

This script:
1. Groups items by variant
2. Calculates statistics (avg, min, max prices)
3. Creates scraped_data_gba.json for website
4. Prepares data for image downloading
"""

import json
from datetime import datetime
from collections import defaultdict

def calculate_variant_stats(items):
    """Calculate price statistics for a list of items"""
    if not items:
        return {
            'avg_price': 0,
            'min_price': 0,
            'max_price': 0,
            'count': 0
        }

    prices = [item['price'] for item in items]
    return {
        'avg_price': round(sum(prices) / len(prices), 2),
        'min_price': min(prices),
        'max_price': max(prices),
        'count': len(items)
    }

def process_gba_data():
    """Process GBA data and create output files"""

    # Load data with corrected dates
    print("Loading GBA data with corrected dates...")
    with open('gba_kept_items_with_dates.json', 'r') as f:
        data = json.load(f)

    items = data['items']
    print(f"Total items: {len(items)}")

    # Group by variant
    variants = defaultdict(list)
    for item in items:
        variant = item['assigned_variant']
        # Transform to match expected format
        item_data = {
            'item_id': item['id'],
            'title': item['title'],
            'price': item['price'],
            'sold_date': item['date'],
            'condition': item['condition'],
            'url': item['url']
        }
        variants[variant].append(item_data)

    print(f"\nVariants found: {len(variants)}")

    # Build output structure
    output_data = {}

    for variant_key, variant_items in sorted(variants.items()):
        stats = calculate_variant_stats(variant_items)

        output_data[variant_key] = {
            'name': format_variant_name(variant_key),
            'avg_price': stats['avg_price'],
            'min_price': stats['min_price'],
            'max_price': stats['max_price'],
            'count': stats['count'],
            'items': variant_items
        }

        print(f"  {variant_key}: {stats['count']} items, avg {stats['avg_price']}€")

    # Save main GBA data file
    output_file = 'scraped_data_gba.json'
    with open(output_file, 'w') as f:
        json.dump(output_data, f, indent=2, ensure_ascii=False)

    print(f"\n✅ Saved to: {output_file}")

    # Display summary
    total_items = sum(v['count'] for v in output_data.values())
    total_variants = len(output_data)

    print(f"\n{'='*60}")
    print(f"GBA DATA READY FOR LAUNCH")
    print(f"{'='*60}")
    print(f"Total variants: {total_variants}")
    print(f"Total items: {total_items}")
    print(f"Average per variant: {total_items / total_variants:.1f}")
    print(f"\nVariant breakdown:")
    for variant_key in sorted(output_data.keys()):
        v = output_data[variant_key]
        print(f"  {v['name']}: {v['count']} items @ {v['avg_price']}€ avg")

    print(f"\n{'='*60}")
    print("Next steps:")
    print("1. Download images: python3 download_listing_images.py scraped_data_gba.json game-boy-advance")
    print("2. Update config.json with GBA variants")
    print("3. Generate GBA pages: python3 update_site_for_gba.py")
    print(f"{'='*60}\n")

    return output_data

def format_variant_name(variant_key):
    """
    Format variant key for display on website.
    Drops "standard-" prefix and formats nicely.

    Examples:
    - standard-fuchsia → Fuchsia
    - standard-indigo → Indigo
    - sp-pearl-blue → SP Pearl Blue
    - micro-famicom → Micro Famicom
    """
    # Remove "standard-" prefix
    if variant_key.startswith('standard-'):
        name = variant_key.replace('standard-', '')
    else:
        name = variant_key

    # Split on hyphens and capitalize
    parts = name.split('-')

    # Special case for "SP" and "Micro"
    formatted_parts = []
    for part in parts:
        if part.lower() == 'sp':
            formatted_parts.append('SP')
        elif part.lower() in ['micro', 'famicom', 'nes', 'donkey', 'kong', 'mario', 'zelda', 'pokemon', 'groudon', 'ruby']:
            formatted_parts.append(part.capitalize())
        else:
            formatted_parts.append(part.capitalize())

    return ' '.join(formatted_parts)

if __name__ == '__main__':
    process_gba_data()
