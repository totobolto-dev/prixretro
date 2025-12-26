#!/usr/bin/env python3
"""
Enhanced Garbage Data Filter for PrixRetro
Removes Game Boy Advance items and other non-Game Boy Color listings
"""

import json
import re

def is_garbage_item(title, description=""):
    """Check if an item is garbage data (not a Game Boy Color)"""
    
    # Convert to lowercase for easier matching
    title_lower = title.lower()
    desc_lower = description.lower() if description else ""
    combined = f"{title_lower} {desc_lower}"
    
    # Game Boy Advance items (wrong console)
    gba_patterns = [
        r'gameboy advance',
        r'game boy advance', 
        r'gba\b',
        r'advance\s+sp',
        r'nintendo\s+advance'
    ]
    
    for pattern in gba_patterns:
        if re.search(pattern, combined):
            return True, "Game Boy Advance (wrong console)"
    
    # GameCube GameBoy Player (not a handheld)
    gamecube_patterns = [
        r'gamecube.*player',
        r'game\s*cube.*player',
        r'gc.*player',
        r'player.*gamecube'
    ]
    
    for pattern in gamecube_patterns:
        if re.search(pattern, combined):
            return True, "GameCube GameBoy Player (not handheld)"
    
    # Reshells/mods (not original)
    if 'reshell' in combined:
        return True, "Reshell/modification (not original)"
    
    # Parts only items
    parts_patterns = [
        r'pi[eè]ces?\s+d[ée]tach[ée]es?',
        r'parts?\s+only',
        r'for\s+parts',
        r'spare\s+parts?',
        r'r[ée]paration',
        r'repair',
        r'broken.*parts',
        r'pi[eè]ces?\s+seules?'
    ]
    
    for pattern in parts_patterns:
        if re.search(pattern, combined):
            return True, "Parts/repair item"
    
    # Clearly broken items (non-functional)
    broken_patterns = [
        r'ne\s+fonctionne\s+pas',
        r'not\s+working',
        r'broken',
        r'cass[ée]',
        r'dead',
        r'hs\b',  # Hors Service (French for "out of order")
        r'defectueuse?',
        r'en\s+panne'
    ]
    
    for pattern in broken_patterns:
        if re.search(pattern, combined):
            return True, "Non-functional/broken"
    
    # Empty or fake cases
    case_patterns = [
        r'bo[iî]tier?\s+seul',
        r'case\s+only',
        r'shell\s+only',
        r'coque?\s+seule?',
        r'empty\s+case'
    ]
    
    for pattern in case_patterns:
        if re.search(pattern, combined):
            return True, "Case/shell only"
    
    # Games included with console (might skew price)
    # Only flag if it's clearly a bundle with many games
    if re.search(r'\+.*\d+.*jeux?', combined) or re.search(r'avec.*\d+.*jeux?', combined):
        return True, "Large game bundle (price skewed)"
    
    return False, ""

def clean_garbage_data():
    """Remove garbage items from clean data"""
    
    # Load current clean data
    with open('scraped_data_clean.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    print("🧹 Enhanced Garbage Data Filter")
    print("=" * 50)
    
    total_original = 0
    total_removed = 0
    removal_reasons = {}
    
    for variant_key, variant_data in data.items():
        print(f"\n🎮 Processing {variant_data['variant_name']}...")
        
        original_listings = variant_data['listings']
        clean_listings = []
        
        for listing in original_listings:
            is_garbage, reason = is_garbage_item(listing['title'])
            
            if is_garbage:
                print(f"  ❌ REMOVED: {listing['title'][:80]}...")
                print(f"     Reason: {reason}")
                total_removed += 1
                
                if reason not in removal_reasons:
                    removal_reasons[reason] = 0
                removal_reasons[reason] += 1
            else:
                clean_listings.append(listing)
        
        total_original += len(original_listings)
        
        # Update variant data
        variant_data['listings'] = clean_listings
        
        # Recalculate stats
        if clean_listings:
            prices = [listing['price'] for listing in clean_listings]
            variant_data['stats']['listing_count'] = len(clean_listings)
            variant_data['stats']['avg_price'] = int(sum(prices) / len(prices))
            variant_data['stats']['min_price'] = min(prices)
            variant_data['stats']['max_price'] = max(prices)
            
            print(f"  ✅ Kept: {len(clean_listings)} items")
            print(f"     Avg price: {variant_data['stats']['avg_price']}€")
        else:
            print(f"  ⚠️  No items remaining after filtering!")
    
    # Save cleaned data
    with open('scraped_data_ultra_clean.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    
    # Summary
    print("\n" + "=" * 50)
    print("🎯 Garbage Filter Results")
    print("=" * 50)
    print(f"Original items: {total_original}")
    print(f"Removed items: {total_removed}")
    print(f"Clean items: {total_original - total_removed}")
    print(f"Removal rate: {(total_removed / total_original * 100):.1f}%")
    
    print("\n📊 Removal Reasons:")
    for reason, count in sorted(removal_reasons.items(), key=lambda x: x[1], reverse=True):
        print(f"  • {reason}: {count} items")
    
    print(f"\n✅ Ultra-clean data saved to: scraped_data_ultra_clean.json")
    return True

if __name__ == "__main__":
    clean_garbage_data()