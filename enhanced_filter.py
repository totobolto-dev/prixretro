#!/usr/bin/env python3
"""
Enhanced filtering system for Game Boy Color items
Removes parts, accessories, wrong variants, and non-consoles
"""

import json
import re
from collections import defaultdict

def is_console_item(title, description=""):
    """Check if this is actually a console, not parts/accessories"""
    title_lower = title.lower()
    
    # STRONG indicators this is NOT a console
    non_console_keywords = [
        # Parts & Accessories
        'cache pile', 'battery cover', 'couvercle pile', 'door cover',
        'coque', 'shell housing', 'replacement housing', 'coque remplacement',
        'ecran', 'screen lcd', 'backlight', 'ips kit', 'lcd kit',
        'speaker', 'haut parleur', 'bouton', 'buttons set', 'rubber pad',
        'motherboard', 'carte mere', 'pcb board',
        'lens', 'lentille', 'glass lens',
        
        # Cables & Chargers  
        'cable', 'chargeur', 'charger', 'adaptateur secteur', 'power adapter',
        'link cable', 'câble link',
        
        # Empty boxes/manuals
        'boîte vide', 'boite vide', 'empty box', 'box only',
        'notice seule', 'manual only', 'instruction only',
        
        # Modification kits
        'mod kit', 'modification', 'upgrade kit', 'custom kit',
        'trimmed', 'pre-cut', 'ips ready', 'laminated',
        
        # Other accessories
        'light', 'lampe', 'worm light', 'magnifier', 'loupe',
        'carrying case', 'sacoche', 'housse', 'etui',
        'game holder', 'cartridge case', 'organisateur'
    ]
    
    for keyword in non_console_keywords:
        if keyword in title_lower:
            return False, f"non-console ({keyword})"
    
    # Must contain console keywords
    console_keywords = ['console', 'game boy', 'gameboy']
    has_console_keyword = any(keyword in title_lower for keyword in console_keywords)
    
    if not has_console_keyword:
        return False, "no console keyword"
    
    return True, "console"

def classify_variant(title, target_variant):
    """Check if item belongs to the target variant"""
    title_lower = title.lower()
    
    # Variant classification rules
    variant_rules = {
        'violet': {
            'accept': ['violet', 'purple', 'mauve'],
            'reject': ['atomic', 'transparent', 'clear', 'pikachu', 'pokemon']
        },
        'atomic-purple': {
            'accept': ['atomic purple', 'violet transparent', 'clear purple', 'transparent violet'],
            'reject': ['pikachu', 'pokemon', 'solid purple']
        },
        'pikachu': {
            'accept': ['pikachu', 'pokemon edition', 'édition pokemon'],
            'reject': ['atomic', 'transparent', 'clear']
        },
        'jaune': {
            'accept': ['jaune', 'yellow', 'dandelion'],
            'reject': ['pikachu', 'pokemon', 'gold', 'or']
        },
        'rouge': {
            'accept': ['rouge', 'red', 'rosé', 'rose'],
            'reject': ['pikachu', 'pokemon']
        },
        'bleu': {
            'accept': ['bleu', 'blue', 'teal', 'turquoise'],
            'reject': ['pikachu', 'pokemon']
        },
        'vert': {
            'accept': ['vert', 'green', 'kiwi'],
            'reject': ['pikachu', 'pokemon']
        },
        'pokemon-gold-silver': {
            'accept': ['gold', 'silver', 'or', 'argent', 'doré', 'argenté'],
            'reject': ['pikachu edition']
        }
    }
    
    if target_variant not in variant_rules:
        return True, "unknown variant"
    
    rules = variant_rules[target_variant]
    
    # Check if it should be rejected (wrong variant)
    for reject_word in rules['reject']:
        if reject_word in title_lower:
            return False, f"wrong variant ({reject_word})"
    
    # Check if it matches this variant
    for accept_word in rules['accept']:
        if accept_word in title_lower:
            return True, f"matches ({accept_word})"
    
    # For pikachu variant, be very strict
    if target_variant == 'pikachu':
        if 'pikachu' not in title_lower:
            return False, "not pikachu edition"
    
    return True, "no specific variant mentioned"

def is_broken_or_parts(title):
    """Check if item is broken or for parts"""
    title_lower = title.lower()
    
    broken_patterns = [
        r'\bhs\b', r'h\.s\.', 'hors service', 'pour pieces', 'pour pièces',
        'à réparer', 'a reparer', 'broken', 'not working', 'for parts',
        'défectueux', 'defectueux', 'ne fonctionne', 'ne marche',
        'does not work', 'untested', 'pas testé', 'pas teste',
        'dead', 'mort', 'no power', 'no display', 'no sound'
    ]
    
    for pattern in broken_patterns:
        if re.search(pattern, title_lower):
            return True, pattern
    
    return False, None

def filter_scraped_data(input_file, output_file):
    """Apply enhanced filtering to scraped data"""
    
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    filtered_data = {}
    stats = defaultdict(lambda: {'removed': 0, 'kept': 0, 'reasons': defaultdict(int)})
    
    for variant_key, variant_data in data.items():
        if not isinstance(variant_data, dict) or 'listings' not in variant_data:
            filtered_data[variant_key] = variant_data
            continue
        
        filtered_listings = []
        
        for item in variant_data['listings']:
            if not isinstance(item, dict) or 'title' not in item:
                continue
                
            title = item['title']
            
            # Check if it's a console
            is_console, console_reason = is_console_item(title)
            if not is_console:
                stats[variant_key]['removed'] += 1
                stats[variant_key]['reasons'][f"non-console: {console_reason}"] += 1
                continue
            
            # Check if broken/parts
            is_broken, broken_reason = is_broken_or_parts(title)
            if is_broken:
                stats[variant_key]['removed'] += 1
                stats[variant_key]['reasons'][f"broken: {broken_reason}"] += 1
                continue
            
            # Check variant classification
            correct_variant, variant_reason = classify_variant(title, variant_key)
            if not correct_variant:
                stats[variant_key]['removed'] += 1
                stats[variant_key]['reasons'][f"variant: {variant_reason}"] += 1
                continue
            
            # Item passed all filters
            filtered_listings.append(item)
            stats[variant_key]['kept'] += 1
        
        # Update variant data
        filtered_variant = variant_data.copy()
        filtered_variant['listings'] = filtered_listings
        
        # Recalculate stats
        if filtered_listings:
            prices = [item['price'] for item in filtered_listings]
            filtered_variant['stats']['avg_price'] = int(sum(prices) / len(prices))
            filtered_variant['stats']['min_price'] = min(prices)
            filtered_variant['stats']['max_price'] = max(prices)
            filtered_variant['stats']['listing_count'] = len(filtered_listings)
        
        filtered_data[variant_key] = filtered_variant
    
    # Save filtered data
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(filtered_data, f, indent=2, ensure_ascii=False)
    
    # Print stats
    print("============================================================")
    print("🧹 Enhanced Filtering Results")
    print("============================================================")
    
    for variant, variant_stats in stats.items():
        total_original = variant_stats['kept'] + variant_stats['removed']
        kept_pct = (variant_stats['kept'] / total_original * 100) if total_original > 0 else 0
        
        print(f"\n📊 {variant}:")
        print(f"   Original: {total_original} items")
        print(f"   Kept: {variant_stats['kept']} items ({kept_pct:.1f}%)")
        print(f"   Removed: {variant_stats['removed']} items")
        
        if variant_stats['reasons']:
            print("   Removal reasons:")
            for reason, count in sorted(variant_stats['reasons'].items(), key=lambda x: x[1], reverse=True):
                print(f"     • {reason}: {count}")
    
    print(f"\n💾 Filtered data saved to: {output_file}")
    print("✅ Enhanced filtering complete!")

if __name__ == "__main__":
    filter_scraped_data('scraped_data_clean.json', 'scraped_data_enhanced.json')