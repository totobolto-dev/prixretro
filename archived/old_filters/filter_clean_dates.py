#!/usr/bin/env python3
"""
Filter out fake dates and rebuild price history with real data only
"""

import json
from collections import defaultdict
from datetime import datetime

def filter_clean_dates(input_file, output_file):
    """Remove items with fake dates and rebuild stats"""
    
    with open(input_file, 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Known fake dates (scraping dates)
    fake_dates = ['2025-12-19', '2025-12-20']  # Add both scraping day and today
    filtered_data = {}
    
    print("============================================================")
    print("🧹 Filtering Fake Dates - Real Price History Only")
    print("============================================================")
    
    for variant_key, variant_data in data.items():
        if not isinstance(variant_data, dict) or 'listings' not in variant_data:
            filtered_data[variant_key] = variant_data
            continue
        
        all_items = variant_data['listings']
        
        # Filter out fake dates (scraping dates)
        clean_items = []
        for item in all_items:
            if isinstance(item, dict) and item.get('sold_date'):
                sold_date = item['sold_date']
                # Remove items with fake dates or suspicious dates
                is_fake = any(fake_date in sold_date for fake_date in fake_dates)
                if (not is_fake and 
                    len(sold_date) >= 8 and 
                    sold_date >= '2025-01-01'):  # Reasonable date range
                    clean_items.append(item)
        
        print(f"\n📊 {variant_key}:")
        print(f"   Original: {len(all_items)} items")
        print(f"   Clean: {len(clean_items)} items ({len(clean_items)/len(all_items)*100:.1f}% real data)")
        
        if len(clean_items) < 5:
            print(f"   ⚠️  Warning: Only {len(clean_items)} clean items - limited data")
        
        # Update variant data
        filtered_variant = variant_data.copy()
        filtered_variant['listings'] = clean_items
        
        # Recalculate stats with clean data only
        if clean_items:
            prices = [item['price'] for item in clean_items]
            filtered_variant['stats']['avg_price'] = int(sum(prices) / len(prices))
            filtered_variant['stats']['min_price'] = min(prices)
            filtered_variant['stats']['max_price'] = max(prices)
            filtered_variant['stats']['listing_count'] = len(clean_items)
            
            # Rebuild price history with clean data
            monthly_prices = defaultdict(list)
            for item in clean_items:
                month_key = item['sold_date'][:7]  # YYYY-MM
                if len(month_key) == 7:
                    monthly_prices[month_key].append(item['price'])
            
            # Calculate monthly averages
            price_history = {}
            for month in sorted(monthly_prices.keys()):
                avg_price = sum(monthly_prices[month]) / len(monthly_prices[month])
                price_history[month] = int(avg_price)
                print(f"     {month}: {len(monthly_prices[month])} sales, avg €{avg_price:.0f}")
            
            filtered_variant['stats']['price_history'] = price_history
            
            if not price_history:
                print("     📉 No monthly price history (insufficient date spread)")
        else:
            # No clean items - set minimal stats
            filtered_variant['stats']['avg_price'] = 0
            filtered_variant['stats']['min_price'] = 0
            filtered_variant['stats']['max_price'] = 0
            filtered_variant['stats']['listing_count'] = 0
            filtered_variant['stats']['price_history'] = {}
            print("     ❌ No clean data available")
        
        filtered_data[variant_key] = filtered_variant
    
    # Save filtered data
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(filtered_data, f, indent=2, ensure_ascii=False)
    
    # Summary
    total_clean = sum(len(v.get('listings', [])) for v in filtered_data.values() 
                     if isinstance(v, dict))
    original_total = sum(len(v.get('listings', [])) for v in data.values() 
                        if isinstance(v, dict))
    
    print(f"\n============================================================")
    print(f"✅ Clean Data Summary")
    print(f"============================================================")
    print(f"Original items: {original_total}")
    print(f"Clean items: {total_clean}")
    print(f"Quality ratio: {total_clean/original_total*100:.1f}% real data")
    print(f"💾 Clean data saved to: {output_file}")
    
    variants_with_history = sum(1 for v in filtered_data.values() 
                               if isinstance(v, dict) and 
                               v.get('stats', {}).get('price_history'))
    print(f"📈 Variants with price history: {variants_with_history}/8")
    print()
    print("🎯 Ready for real price trend graphs!")

if __name__ == "__main__":
    filter_clean_dates('scraped_data_clean.json', 'scraped_data_real_dates.json')