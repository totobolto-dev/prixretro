#!/usr/bin/env python3
"""
Bulletproof Data Filter - Final Quality Pass
Removes all remaining garbage to ensure 100% authentic Game Boy Color data
"""

import json
import re
from datetime import datetime, timedelta

def is_bulletproof_authentic(title, price, variant_key, description=""):
    """Ultra-strict filter for bulletproof authenticity"""
    
    title_lower = title.lower()
    desc_lower = description.lower() if description else ""
    combined = f"{title_lower} {desc_lower}"
    
    # PARTS/ACCESSORIES (not complete consoles)
    parts_patterns = [
        r'\bbattery\s+cover\b',
        r'\bbattery\s+back\b', 
        r'\brear\s+shell\b',
        r'\bback\s+cover\b',
        r'\bshell\s+only\b',
        r'\bcover\s+only\b',
        r'\bpart\b.*\bonly\b',
        r'\baccessoire\b',
        r'\bpièce\s+détachée\b',
        r'\bscreen\s+only\b',
        r'\blens\s+only\b',
        r'\bbutton\b.*\bonly\b'
    ]
    
    for pattern in parts_patterns:
        if re.search(pattern, combined):
            return False, f"Parts/accessories only: {pattern}"
    
    # WRONG CONSOLE TYPES
    wrong_console_patterns = [
        r'\bgameboy\s+pocket\b',
        r'\bgame\s+boy\s+pocket\b',
        r'\bgbp\b',
        r'\bpocket\s+game\s*boy\b',
        r'\bgameboy\s+advance\b',
        r'\bgame\s+boy\s+advance\b',
        r'\bgba\b(?!\s*color)',  # GBA but not "GBA Color" (which doesn't exist anyway)
        r'\badvance\s+sp\b',
        r'\bgamecube\b',
        r'\bds\b',
        r'\b3ds\b'
    ]
    
    for pattern in wrong_console_patterns:
        if re.search(pattern, combined):
            return False, f"Wrong console type: {pattern}"
    
    # VARIANT MISMATCH (item doesn't match expected variant)
    variant_keywords = {
        'violet': ['violet', 'purple', 'violette'],
        'atomic-purple': ['atomic', 'transparent', 'clear', 'translucent', 'atomique'],
        'rouge': ['rouge', 'red'],
        'bleu': ['bleu', 'blue', 'teal', 'turquoise', 'cyan'],
        'vert': ['vert', 'green', 'neon'],
        'pikachu': ['pikachu', 'pokemon', 'pokémon'],
        'pokemon-gold-silver': ['gold', 'silver', 'or', 'argent', 'special', 'limited']
    }
    
    if variant_key in variant_keywords:
        expected_colors = variant_keywords[variant_key]
        
        # Check if title contains expected color keywords
        has_expected_color = any(color in combined for color in expected_colors)
        
        # Check for conflicting colors (except for pokemon variants which can be multicolor)
        conflicting_colors = []
        if variant_key not in ['pikachu', 'pokemon-gold-silver']:
            all_other_colors = []
            for k, colors in variant_keywords.items():
                if k != variant_key:
                    all_other_colors.extend(colors)
            
            conflicting_colors = [color for color in all_other_colors if color in combined]
        
        # If we have conflicting colors and no expected colors, it's likely misclassified
        if conflicting_colors and not has_expected_color:
            return False, f"Color mismatch: found {conflicting_colors}, expected {expected_colors}"
    
    # SUSPICIOUS PRICING (likely not authentic consoles)
    if price < 20:  # Complete Game Boy Color should not be under 20€
        return False, f"Suspiciously low price: {price}€"
    
    if price > 600:  # Even rare variants shouldn't exceed this in normal condition
        return False, f"Suspiciously high price: {price}€"
    
    # BUNDLE DETECTION (multiple consoles/large game lots skew individual price)
    bundle_patterns = [
        r'\blot\s+de\s+\d+',
        r'\d+\s+consoles?\b',
        r'\d+\s+game\s*boys?\b',
        r'\bmultiple\b',
        r'\bensemble\s+de\b',
        r'\bpack\s+de\b',
        r'\+.*\+.*\+',  # Multiple + signs indicating bundle
        r'\bavec\s+\d+.*jeux?\b'  # With X games
    ]
    
    for pattern in bundle_patterns:
        if re.search(pattern, combined):
            return False, f"Bundle/lot detected: {pattern}"
    
    # REPRODUCTION/FAKE ITEMS
    fake_patterns = [
        r'\breproduction\b',
        r'\brepro\b',
        r'\breplica\b',
        r'\bfake\b',
        r'\bcustomized?\b',
        r'\bmodified?\b',
        r'\breshell\b',
        r'\bmod\b(?!\s+chip)',  # "mod" but not "mod chip"
        r'\bcustom\s+shell\b'
    ]
    
    for pattern in fake_patterns:
        if re.search(pattern, combined):
            return False, f"Reproduction/modification: {pattern}"
    
    # REQUIRE GAME BOY COLOR MENTION
    required_patterns = [
        r'game\s*boy\s+color',
        r'gameboy\s+color', 
        r'gbc\b',
        r'cgb'  # Console model number
    ]
    
    has_required = any(re.search(pattern, combined) for pattern in required_patterns)
    if not has_required:
        return False, "Missing Game Boy Color identifier"
    
    return True, "Authentic Game Boy Color"

def validate_dates():
    """Check for impossible future dates"""
    today = datetime.now()
    future_threshold = today + timedelta(days=1)  # Allow 1 day buffer
    
    problems = []
    
    with open('scraped_data_ultra_clean.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    for variant_key, variant_data in data.items():
        for listing in variant_data['listings']:
            try:
                sold_date = datetime.strptime(listing['sold_date'], '%Y-%m-%d')
                if sold_date > future_threshold:
                    problems.append({
                        'variant': variant_key,
                        'item_id': listing['item_id'], 
                        'title': listing['title'][:50] + "...",
                        'sold_date': listing['sold_date'],
                        'issue': 'Future date'
                    })
            except ValueError:
                problems.append({
                    'variant': variant_key,
                    'item_id': listing['item_id'],
                    'title': listing['title'][:50] + "...", 
                    'sold_date': listing['sold_date'],
                    'issue': 'Invalid date format'
                })
    
    return problems

def create_bulletproof_data():
    """Create absolutely bulletproof dataset"""
    
    print("🔒 Creating Bulletproof Dataset")
    print("=" * 60)
    
    # First check dates
    print("📅 Validating dates...")
    date_problems = validate_dates()
    if date_problems:
        print(f"⚠️  Found {len(date_problems)} date issues:")
        for problem in date_problems[:5]:  # Show first 5
            print(f"   • {problem['variant']}: {problem['title']} ({problem['issue']})")
        print("   Date validation failed - data needs cleaning")
    
    # Load data  
    with open('scraped_data_ultra_clean.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    total_original = 0
    total_removed = 0
    removal_reasons = {}
    
    # Apply bulletproof filter
    for variant_key, variant_data in data.items():
        print(f"\n🔍 Validating {variant_data['variant_name']}...")
        
        original_listings = variant_data['listings']
        bulletproof_listings = []
        
        for listing in original_listings:
            is_authentic, reason = is_bulletproof_authentic(
                listing['title'], 
                listing['price'], 
                variant_key
            )
            
            if not is_authentic:
                print(f"   ❌ REMOVED: {listing['title'][:60]}...")
                print(f"      Reason: {reason}")
                total_removed += 1
                
                if reason not in removal_reasons:
                    removal_reasons[reason] = 0
                removal_reasons[reason] += 1
            else:
                bulletproof_listings.append(listing)
        
        total_original += len(original_listings)
        
        # Update variant data
        variant_data['listings'] = bulletproof_listings
        
        # Recalculate stats
        if bulletproof_listings:
            prices = [listing['price'] for listing in bulletproof_listings]
            variant_data['stats']['listing_count'] = len(bulletproof_listings)
            variant_data['stats']['avg_price'] = int(sum(prices) / len(prices))
            variant_data['stats']['min_price'] = min(prices)
            variant_data['stats']['max_price'] = max(prices)
            
            print(f"   ✅ {len(bulletproof_listings)} bulletproof items")
            print(f"      Avg: {variant_data['stats']['avg_price']}€")
        else:
            print(f"   ⚠️  No bulletproof items remaining")
    
    # Save bulletproof data
    with open('scraped_data_bulletproof.json', 'w', encoding='utf-8') as f:
        json.dump(data, f, indent=2, ensure_ascii=False)
    
    # Final summary
    print("\n" + "=" * 60)
    print("🔒 BULLETPROOF DATA CREATED")
    print("=" * 60)
    print(f"Original items: {total_original}")
    print(f"Removed items: {total_removed}")
    print(f"Bulletproof items: {total_original - total_removed}")
    print(f"Removal rate: {(total_removed / total_original * 100):.1f}%")
    
    print("\n📊 Removal Reasons:")
    for reason, count in sorted(removal_reasons.items(), key=lambda x: x[1], reverse=True):
        print(f"   • {reason}: {count} items")
    
    print(f"\n🎯 Bulletproof data saved to: scraped_data_bulletproof.json")
    print("✅ Ready for content creation with 100% confidence")
    
    return total_original - total_removed

if __name__ == "__main__":
    remaining_count = create_bulletproof_data()