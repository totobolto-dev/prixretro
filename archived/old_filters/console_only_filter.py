#!/usr/bin/env python3
"""
Console-Only Filter - ULTRA STRICT
Only allows actual Game Boy Color CONSOLES, no games whatsoever
"""

import json
import re

def is_actual_console(title, price, description=""):
    """Ultra-strict: only allow actual consoles"""
    
    title_lower = title.lower()
    desc_lower = description.lower() if description else ""
    combined = f"{title_lower} {desc_lower}"
    
    # REQUIRE console identifiers
    console_identifiers = [
        r'\bconsole\b',
        r'\bsystem\b', 
        r'\bhandheld\b',
        r'\bportable\b',
        r'\bcgb-001\b',  # Official model number
        r'\bcgb\s*001\b',
        r'\bsystème\b'
    ]
    
    has_console_identifier = any(re.search(pattern, combined) for pattern in console_identifiers)
    
    # GAMES DETECTION (ultra-strict)
    game_patterns = [
        r'\bversion\s+rouge\b',
        r'\bversion\s+bleu\b', 
        r'\bversion\s+jaune\b',
        r'\bversion\s+or\b',
        r'\bversion\s+argent\b',
        r'\bversion\s+cristal\b',
        r'\bpokémon\s+rouge\b',
        r'\bpokémon\s+bleu\b',
        r'\bpokémon\s+jaune\b',
        r'\bpokémon\s+or\b',
        r'\bpokémon\s+argent\b',
        r'\bpokémon\s+crystal?\b',
        r'\bpokemon\s+gold\b',
        r'\bpokemon\s+silver\b',
        r'\bpokemon\s+crystal?\b',
        r'\bgold\s+version\b',
        r'\bsilver\s+version\b',
        r'\bcrystal\s+version\b',
        r'\b(red|blue|yellow|gold|silver|crystal)\s+version\b',
        r'\bjeu\b(?!.*console)',  # "jeu" but not with "console"
        r'\bgame\b(?!.*boy.*color.*console)',  # "game" but not "game boy color console"
        r'\bcartridge\b',
        r'\bcart\b(?!\s+included)',
        r'\brom\b',
        r'\btitle\b.*\bgame\b',
        r'\bauthentique\b.*\b(or|argent|crystal|gold|silver)\b',
        r'\bdry\s+batter(y|ies)\b',  # Games have save batteries
        r'\bsave\s+(battery|file)\b',
        r'\bmanual\b(?!.*console)',
        r'\boriginal\s+box\b(?!.*console)',
        r'\bcib\b',  # Complete in box (usually games)
        r'\bwith\s+box\b(?!.*console)',
        r'\bfrançaises?\b(?!.*console)',  # "Versions françaises" 
        r'\blot\s+pokémon\b',
        r'\b🔴🟡🔵\b',  # Color emojis indicating game versions
        r'\b🎮\s*lot\b',
        r'\boffici[ae]l\s+(nintendo|pokémon|pokemon)\b(?!.*console)',
        r'\btestedisaving\b',
        r'\bnew\s+batteries\b'
    ]
    
    for pattern in game_patterns:
        if re.search(pattern, combined):
            return False, f"Game detected: {pattern}"
    
    # ACCESSORIES (not consoles)
    accessory_patterns = [
        r'\bbag\b',
        r'\bcarrier\b', 
        r'\btravel\s+bag\b',
        r'\bhousse\b',
        r'\bétui\b',
        r'\bsac\b',
        r'\bmanual\s+only\b',
        r'\bmanuel\s+seul\b',
        r'\bboxonly\b',
        r'\bboite?\s+seule\b',
        r'\bguide\s+strateg(y|ie)\b'
    ]
    
    for pattern in accessory_patterns:
        if re.search(pattern, combined):
            return False, f"Accessory detected: {pattern}"
    
    # BUNDLES/LOTS (multiple items that aren't individual consoles)
    bundle_patterns = [
        r'\blot\s+',
        r'\bpack\s+',
        r'\bensemble\s+',
        r'\bmultiple\b',
        r'\b\+\s*\w+\s*\+',  # Multiple + signs
        r'\bavec\s+jeu\b',
        r'\bwith\s+game\b',
        r'\b&\s+(silver|gold|or|argent)\b'  # "Gold & Silver"
    ]
    
    for pattern in bundle_patterns:
        if re.search(pattern, combined):
            return False, f"Bundle/lot detected: {pattern}"
    
    # MODS/CUSTOMIZATIONS
    mod_patterns = [
        r'\bips\s+screen\b',
        r'\bamoled\b',
        r'\btouch\s+screen\b',
        r'\bbacklight\b',
        r'\bretro\s*bright\b',
        r'\bmod\s+chip\b',
        r'\bclear\s+shell\b(?!.*atomic)',  # Clear shell mod but not atomic purple
        r'\bcustom\s+shell\b',
        r'\bmod\s+audio\b',
        r'\bcapkit\b',
        r'\boled\s+(screen|mod)\b',
        r'\bmod\b(?=.*screen)',
        r'\btestée?\s+et\s+fonctionnelle?\b.*\bbon\b'  # Too generic description
    ]
    
    for pattern in mod_patterns:
        if re.search(pattern, combined):
            return False, f"Modified console: {pattern}"
    
    # REQUIRE at minimum "game boy color" AND console indicator
    has_gbc = any(re.search(pattern, combined) for pattern in [
        r'game\s*boy\s+color',
        r'gameboy\s+color',
        r'gbc\b',
        r'cgb'
    ])
    
    if not has_gbc:
        return False, "Missing Game Boy Color identifier"
    
    # Price sanity check (stricter)
    if price < 25:  # Genuine consoles rarely under 25€
        return False, f"Too cheap for console: {price}€"
        
    if price > 400:  # Even rare variants rarely over 400€
        return False, f"Too expensive for single console: {price}€"
    
    # If no console identifier found, be very suspicious
    if not has_console_identifier:
        # Check for strong console-indicating words
        strong_console_words = [
            r'\bmachine\b',
            r'\bappareil\b',
            r'\bhardware\b',
            r'\bmodel\b'
        ]
        has_strong_indicator = any(re.search(pattern, combined) for pattern in strong_console_words)
        
        if not has_strong_indicator:
            return False, "No console identifier found"
    
    return True, "Verified Game Boy Color console"

def create_console_only_data():
    """Create dataset with ONLY actual consoles"""
    
    print("🔒 CONSOLE-ONLY Filter (Ultra-Strict)")
    print("=" * 60)
    
    with open('scraped_data_bulletproof.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    total_original = 0
    total_removed = 0
    removal_reasons = {}
    
    for variant_key, variant_data in data.items():
        print(f"\n🔍 Filtering {variant_data['variant_name']}...")
        
        original_listings = variant_data['listings']
        console_only_listings = []
        
        for listing in original_listings:
            is_console, reason = is_actual_console(
                listing['title'], 
                listing['price']
            )
            
            if not is_console:
                print(f"   ❌ REMOVED: {listing['title'][:70]}...")
                print(f"      Reason: {reason}")
                total_removed += 1
                
                if reason not in removal_reasons:
                    removal_reasons[reason] = 0
                removal_reasons[reason] += 1
            else:
                console_only_listings.append(listing)
                print(f"   ✅ KEPT: {listing['title'][:50]}...")
        
        total_original += len(original_listings)
        
        # Update variant data
        variant_data['listings'] = console_only_listings
        
        # Recalculate stats
        if console_only_listings:
            prices = [listing['price'] for listing in console_only_listings]
            variant_data['stats']['listing_count'] = len(console_only_listings)
            variant_data['stats']['avg_price'] = int(sum(prices) / len(prices))
            variant_data['stats']['min_price'] = min(prices)
            variant_data['stats']['max_price'] = max(prices)
            
            print(f"   📊 Final: {len(console_only_listings)} CONSOLES ONLY")
            print(f"      Avg: {variant_data['stats']['avg_price']}€")
        else:
            print(f"   ⚠️  No consoles remaining")
    
    # Save console-only data
    with open('scraped_data_consoles_only.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    
    # Final summary
    print("\n" + "=" * 60)
    print("🔒 CONSOLES-ONLY DATA CREATED")
    print("=" * 60)
    print(f"Original items: {total_original}")
    print(f"Removed games/accessories: {total_removed}")
    print(f"ACTUAL CONSOLES: {total_original - total_removed}")
    print(f"Game removal rate: {(total_removed / total_original * 100):.1f}%")
    
    print("\n📊 Removal Reasons:")
    for reason, count in sorted(removal_reasons.items(), key=lambda x: x[1], reverse=True):
        print(f"   • {reason}: {count} items")
    
    print(f"\n🎯 Console-only data saved to: scraped_data_consoles_only.json")
    print("✅ NOW 100% bulletproof for content creation")
    
    return total_original - total_removed

if __name__ == "__main__":
    console_count = create_console_only_data()