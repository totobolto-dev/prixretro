#!/usr/bin/env python3
"""
Categorize All Scraped Data
No filtering - just smart categorization for manual review
"""

import json
import re
from collections import defaultdict

def categorize_item(title, price, description=""):
    """Categorize items without filtering - for manual review"""
    
    title_lower = title.lower()
    desc_lower = description.lower() if description else ""
    combined = f"{title_lower} {desc_lower}"
    
    categories = []
    confidence = "unknown"
    
    # CONSOLE DETECTION
    console_indicators = [
        r'\bconsole\b',
        r'\bsystem\b', 
        r'\bhandheld\b',
        r'\bportable\b',
        r'\bcgb-001\b',
        r'\bcgb\s*001\b',
        r'\bsystème\b',
        r'\bmachine\b'
    ]
    
    has_console_words = sum(1 for pattern in console_indicators if re.search(pattern, combined))
    
    if has_console_words >= 2:
        categories.append("CONSOLE_HIGH_CONFIDENCE")
        confidence = "high"
    elif has_console_words == 1:
        categories.append("CONSOLE_MEDIUM_CONFIDENCE") 
        confidence = "medium"
    
    # GAME DETECTION
    game_patterns = [
        r'\bversion\s+(rouge|bleu|jaune|or|argent|crystal)\b',
        r'\bpokémon\s+(rouge|bleu|jaune|or|argent|crystal)\b',
        r'\bpokemon\s+(red|blue|yellow|gold|silver|crystal)\b',
        r'\bjeu\b',
        r'\bgame\b(?!.*boy.*color)',
        r'\bcartridge\b',
        r'\brom\b',
        r'\bauthentique\b.*\b(or|argent|gold|silver)\b',
        r'\bdry\s+batter(y|ies)\b',
        r'\bsave\s+file\b',
        r'\bmanual\b(?!.*console)',
        r'\bcib\b'
    ]
    
    game_matches = sum(1 for pattern in game_patterns if re.search(pattern, combined))
    
    if game_matches >= 2:
        categories.append("GAME_HIGH_CONFIDENCE")
    elif game_matches == 1:
        categories.append("GAME_LOW_CONFIDENCE")
    
    # PARTS/ACCESSORIES
    parts_patterns = [
        r'\bbattery\s+(cover|back)\b',
        r'\bshell\s+only\b',
        r'\bparts?\s+only\b',
        r'\bscreen\s+only\b',
        r'\blens\s+only\b',
        r'\bbutton\b.*\bonly\b',
        r'\baccessoire\b',
        r'\bpi[eè]ce\s+d[ée]tach[ée]e\b',
        r'\bbag\b',
        r'\bcarrier\b',
        r'\btravel\s+bag\b',
        r'\bétui\b',
        r'\bhousse\b'
    ]
    
    if any(re.search(pattern, combined) for pattern in parts_patterns):
        categories.append("PARTS_ACCESSORIES")
    
    # BUNDLES/LOTS
    bundle_patterns = [
        r'\blot\b',
        r'\bpack\b',
        r'\bensemble\b',
        r'\bmultiple\b',
        r'\b\+.*\+\b',
        r'\bavec\s+jeu\b',
        r'\bwith\s+game\b',
        r'\b&\s+(silver|gold|or|argent)\b'
    ]
    
    if any(re.search(pattern, combined) for pattern in bundle_patterns):
        categories.append("BUNDLE_LOT")
    
    # MODIFICATIONS
    mod_patterns = [
        r'\bips\s+screen\b',
        r'\bamoled\b',
        r'\bbacklight\b',
        r'\bmod\b',
        r'\bcustom\b',
        r'\breshell\b',
        r'\bretro\s*bright\b'
    ]
    
    if any(re.search(pattern, combined) for pattern in mod_patterns):
        categories.append("MODIFIED")
    
    # WRONG CONSOLE TYPE
    wrong_console_patterns = [
        r'\bgameboy\s+pocket\b',
        r'\bgame\s+boy\s+pocket\b', 
        r'\bgameboy\s+advance\b',
        r'\bgame\s+boy\s+advance\b',
        r'\bgamecube\b'
    ]
    
    if any(re.search(pattern, combined) for pattern in wrong_console_patterns):
        categories.append("WRONG_CONSOLE_TYPE")
    
    # COLOR VARIANT DETECTION
    color_variants = {
        'violet': ['violet', 'purple', 'violette'],
        'atomic-purple': ['atomic', 'transparent', 'clear', 'translucent'],
        'rouge': ['rouge', 'red'],
        'bleu': ['bleu', 'blue', 'teal', 'turquoise'],
        'vert': ['vert', 'green', 'kiwi'],
        'jaune': ['jaune', 'yellow'],
        'pikachu': ['pikachu', 'pokemon'],
        'pokemon-gold-silver': ['gold', 'silver', 'or', 'argent']
    }
    
    detected_variants = []
    for variant, colors in color_variants.items():
        if any(color in combined for color in colors):
            detected_variants.append(variant)
    
    if len(detected_variants) > 1:
        categories.append("MULTIPLE_VARIANTS")
    
    # CONDITION ASSESSMENT
    condition_good = [
        r'\bneuf\b', r'\bnew\b', r'\bexcellent\b', r'\bmint\b',
        r'\btbe\b', r'\btrès bon état\b', r'\bbon état\b'
    ]
    
    condition_bad = [
        r'\bd[ée]faut\b', r'\bcass[ée]\b', r'\babîm[ée]\b', 
        r'\ben panne\b', r'\bhs\b', r'\bpour pi[eè]ces\b',
        r'\bfor parts\b', r'\bbroken\b', r'\bdamaged\b'
    ]
    
    if any(re.search(pattern, combined) for pattern in condition_good):
        categories.append("GOOD_CONDITION")
    elif any(re.search(pattern, combined) for pattern in condition_bad):
        categories.append("BAD_CONDITION")
    
    # PRICE ANALYSIS
    if price < 20:
        categories.append("VERY_LOW_PRICE")
    elif price < 40:
        categories.append("LOW_PRICE")
    elif price > 200:
        categories.append("HIGH_PRICE")
    elif price > 400:
        categories.append("VERY_HIGH_PRICE")
    
    # If no categories detected
    if not categories:
        categories.append("UNCLASSIFIED")
    
    return {
        'categories': categories,
        'confidence': confidence,
        'detected_variants': detected_variants,
        'price_range': get_price_range(price)
    }

def get_price_range(price):
    """Get price range category"""
    if price < 30:
        return "0-30€"
    elif price < 60:
        return "30-60€"
    elif price < 100:
        return "60-100€"
    elif price < 150:
        return "100-150€"
    elif price < 250:
        return "150-250€"
    else:
        return "250€+"

def analyze_all_data():
    """Analyze all scraped data without filtering"""
    
    print("📊 CATEGORIZING ALL SCRAPED DATA")
    print("=" * 60)
    print("🔍 No filtering - just smart categorization for manual review")
    
    # Load original raw data
    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Statistics tracking
    stats = {
        'total_items': 0,
        'by_category': defaultdict(int),
        'by_variant': defaultdict(int),
        'by_price_range': defaultdict(int),
        'by_confidence': defaultdict(int)
    }
    
    # Categorized data structure
    categorized_data = {}
    all_items = []
    
    for variant_key, variant_data in data.items():
        print(f"\n🎮 Analyzing {variant_data['variant_name']}...")
        
        categorized_listings = []
        
        for listing in variant_data['listings']:
            # Categorize each item
            analysis = categorize_item(listing['title'], listing['price'])
            
            # Add analysis to listing
            categorized_listing = {
                **listing,
                'analysis': analysis
            }
            
            categorized_listings.append(categorized_listing)
            all_items.append({
                'variant': variant_key,
                'title': listing['title'][:80] + "..." if len(listing['title']) > 80 else listing['title'],
                'price': listing['price'],
                'categories': analysis['categories'],
                'confidence': analysis['confidence'],
                'url': listing['url']
            })
            
            # Update statistics
            stats['total_items'] += 1
            stats['by_variant'][variant_key] += 1
            stats['by_confidence'][analysis['confidence']] += 1
            stats['by_price_range'][analysis['price_range']] += 1
            
            for category in analysis['categories']:
                stats['by_category'][category] += 1
        
        # Update variant data with categorized listings
        categorized_data[variant_key] = {
            **variant_data,
            'listings': categorized_listings
        }
        
        print(f"   📋 Categorized {len(categorized_listings)} items")
    
    # Save categorized data
    with open('scraped_data_categorized.json', 'w', encoding='utf-8') as f:
        json.dump(categorized_data, f, indent=2, ensure_ascii=False)
    
    # Create manual review file
    create_manual_review_file(all_items, stats)
    
    # Print summary
    print_analysis_summary(stats)
    
    return categorized_data

def create_manual_review_file(all_items, stats):
    """Create a human-readable file for manual review"""
    
    # Sort items by categories for easier review
    by_category = defaultdict(list)
    for item in all_items:
        primary_category = item['categories'][0] if item['categories'] else 'UNCLASSIFIED'
        by_category[primary_category].append(item)
    
    # Write manual review file
    with open('MANUAL_REVIEW.md', 'w', encoding='utf-8') as f:
        f.write("# Manual Review - Game Boy Color Listings\n\n")
        f.write("## Summary Statistics\n\n")
        f.write(f"**Total Items**: {stats['total_items']}\n\n")
        
        f.write("### By Category\n")
        for category, count in sorted(stats['by_category'].items(), key=lambda x: x[1], reverse=True):
            f.write(f"- **{category}**: {count} items\n")
        
        f.write("\n### By Confidence Level\n")
        for confidence, count in sorted(stats['by_confidence'].items(), key=lambda x: x[1], reverse=True):
            f.write(f"- **{confidence}**: {count} items\n")
        
        f.write("\n### By Price Range\n")
        for price_range, count in sorted(stats['by_price_range'].items(), key=lambda x: x[1], reverse=True):
            f.write(f"- **{price_range}**: {count} items\n")
        
        f.write("\n---\n\n## Items by Category\n\n")
        
        # List items by category
        priority_categories = [
            'CONSOLE_HIGH_CONFIDENCE',
            'CONSOLE_MEDIUM_CONFIDENCE', 
            'GAME_HIGH_CONFIDENCE',
            'GAME_LOW_CONFIDENCE',
            'PARTS_ACCESSORIES',
            'BUNDLE_LOT',
            'MODIFIED',
            'WRONG_CONSOLE_TYPE'
        ]
        
        for category in priority_categories:
            if category in by_category:
                items = by_category[category]
                f.write(f"### {category} ({len(items)} items)\n\n")
                
                for item in sorted(items, key=lambda x: x['price']):
                    f.write(f"**{item['price']}€** - {item['title']}\n")
                    f.write(f"- Variant: {item['variant']}\n")
                    f.write(f"- Categories: {', '.join(item['categories'])}\n")
                    f.write(f"- Confidence: {item['confidence']}\n")
                    f.write(f"- URL: {item['url']}\n\n")
                
                f.write("---\n\n")

def print_analysis_summary(stats):
    """Print analysis summary"""
    
    print("\n" + "=" * 60)
    print("📊 CATEGORIZATION COMPLETE")
    print("=" * 60)
    
    print(f"\n🎯 Total Items Analyzed: {stats['total_items']}")
    
    print("\n📋 Top Categories:")
    for category, count in sorted(stats['by_category'].items(), key=lambda x: x[1], reverse=True)[:10]:
        percentage = (count / stats['total_items']) * 100
        print(f"   • {category}: {count} items ({percentage:.1f}%)")
    
    print("\n🎮 Items by Variant:")
    for variant, count in sorted(stats['by_variant'].items(), key=lambda x: x[1], reverse=True):
        print(f"   • {variant}: {count} items")
    
    print("\n💰 Price Distribution:")
    for price_range, count in sorted(stats['by_price_range'].items(), key=lambda x: x[1], reverse=True):
        print(f"   • {price_range}: {count} items")
    
    print(f"\n✅ Files created:")
    print(f"   • scraped_data_categorized.json - Full data with categories")
    print(f"   • MANUAL_REVIEW.md - Human-readable review file")
    
    print(f"\n🔍 Next steps:")
    print(f"   1. Review MANUAL_REVIEW.md file")
    print(f"   2. Manually classify console vs game vs accessories")
    print(f"   3. Build clean dataset based on manual review")
    print(f"   4. Add Game Boy Color specs & variant information")

if __name__ == "__main__":
    analyze_all_data()