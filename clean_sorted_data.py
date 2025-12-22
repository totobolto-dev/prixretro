#!/usr/bin/env python3
"""
Clean sorted data - Remove unwanted variants
"""

import json
import sys

def clean_sorted_data(input_file, output_file):
    """Remove unwanted variants from sorted data"""

    # Variants to remove
    REMOVE_VARIANTS = [
        'pikachu',
        'pokemon-gold-silver',
        'transparent',
        'bleu-transparent'
    ]

    print("📂 Loading sorted data...")
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    print(f"\n📊 Original data:")
    print(f"   Total items: {data['summary']['total']}")
    print(f"   Keep: {data['summary']['keep']}")
    print(f"   Custom variants: {data.get('custom_variants', [])}")

    # Filter items
    original_items = data['items']
    cleaned_items = []
    removed_count = 0

    for item in original_items:
        variant = item.get('assigned_variant', '')

        # If item is marked as KEEP and has a variant to remove, change to REJECT
        if item['status'] == 'keep' and variant in REMOVE_VARIANTS:
            print(f"   🗑️  Removing: {variant} - {item['title'][:60]}")
            item['status'] = 'reject'  # Mark as rejected instead
            item['assigned_variant'] = ''  # Clear variant
            removed_count += 1

        cleaned_items.append(item)

    # Remove unwanted variants from custom_variants list
    original_custom = data.get('custom_variants', [])
    cleaned_custom = [v for v in original_custom if v not in REMOVE_VARIANTS]

    removed_custom = [v for v in original_custom if v in REMOVE_VARIANTS]
    if removed_custom:
        print(f"\n🗑️  Removed custom variants: {', '.join(removed_custom)}")

    # Update data
    data['items'] = cleaned_items
    data['custom_variants'] = cleaned_custom

    # Recalculate summary
    data['summary'] = {
        'total': len(cleaned_items),
        'keep': len([i for i in cleaned_items if i['status'] == 'keep']),
        'bundle': len([i for i in cleaned_items if i['status'] == 'bundle']),
        'parts': len([i for i in cleaned_items if i['status'] == 'parts']),
        'reject': len([i for i in cleaned_items if i['status'] == 'reject'])
    }

    # Save cleaned data
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    print(f"\n✅ Cleaned data:")
    print(f"   Items moved to reject: {removed_count}")
    print(f"   Keep: {data['summary']['keep']}")
    print(f"   Bundle: {data['summary']['bundle']}")
    print(f"   Parts: {data['summary']['parts']}")
    print(f"   Reject: {data['summary']['reject']}")
    print(f"   Custom variants: {cleaned_custom}")
    print(f"\n💾 Saved to: {output_file}")

if __name__ == "__main__":
    input_file = sys.argv[1] if len(sys.argv) > 1 else 'sorted_items_2025-12-22.json'
    output_file = 'sorted_items_cleaned.json'
    clean_sorted_data(input_file, output_file)
