#!/usr/bin/env python3
"""
Deduplicate scraped data and create compact display
"""

import json
import hashlib
from collections import defaultdict

def generate_item_hash(listing):
    """Generate hash for deduplication"""
    # Use title + price + condition for similarity matching
    key_data = f"{listing['title'].lower().strip()}{listing['price']}{listing['condition'].lower().strip()}"
    return hashlib.md5(key_data.encode()).hexdigest()

def deduplicate_data():
    """Remove duplicates from categorized data"""
    
    print("🧹 Deduplicating Scraped Data")
    print("=" * 50)
    
    # Load categorized data
    with open('scraped_data_categorized.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    seen_hashes = set()
    total_original = 0
    total_deduplicated = 0
    duplicate_count = 0
    
    deduplicated_data = {}
    
    for variant_key, variant_data in data.items():
        print(f"\n🔍 Deduplicating {variant_data['variant_name']}...")
        
        original_listings = variant_data['listings']
        deduplicated_listings = []
        variant_duplicates = 0
        
        for listing in original_listings:
            item_hash = generate_item_hash(listing)
            
            if item_hash not in seen_hashes:
                seen_hashes.add(item_hash)
                deduplicated_listings.append(listing)
            else:
                variant_duplicates += 1
                duplicate_count += 1
                print(f"   🔄 Duplicate: {listing['title'][:60]}... ({listing['price']}€)")
        
        total_original += len(original_listings)
        total_deduplicated += len(deduplicated_listings)
        
        # Update variant data
        deduplicated_data[variant_key] = {
            **variant_data,
            'listings': deduplicated_listings
        }
        
        # Update stats
        if deduplicated_listings:
            prices = [listing['price'] for listing in deduplicated_listings]
            deduplicated_data[variant_key]['stats']['listing_count'] = len(deduplicated_listings)
            deduplicated_data[variant_key]['stats']['avg_price'] = int(sum(prices) / len(prices))
            deduplicated_data[variant_key]['stats']['min_price'] = min(prices)
            deduplicated_data[variant_key]['stats']['max_price'] = max(prices)
        
        print(f"   ✅ Kept: {len(deduplicated_listings)} unique items")
        print(f"   🔄 Removed: {variant_duplicates} duplicates")
    
    # Save deduplicated data
    with open('scraped_data_deduplicated.json', 'w', encoding='utf-8') as f:
        json.dump(deduplicated_data, f, indent=2, ensure_ascii=False)
    
    # Summary
    print("\n" + "=" * 50)
    print("🧹 DEDUPLICATION COMPLETE")
    print("=" * 50)
    print(f"Original items: {total_original}")
    print(f"Unique items: {total_deduplicated}")
    print(f"Duplicates removed: {duplicate_count}")
    print(f"Deduplication rate: {(duplicate_count / total_original * 100):.1f}%")
    
    print(f"\n✅ Deduplicated data saved to: scraped_data_deduplicated.json")
    
    return deduplicated_data

def update_template_for_compact_display():
    """Update template to use compact line-by-line display instead of cards"""
    
    print("\n🎨 Updating template for compact display...")
    
    # Read current template
    with open('template-v3.html', 'r', encoding='utf-8') as f:
        template = f.read()
    
    # New compact listings CSS
    compact_css = '''
        /* Compact Listings Display */
        .listings-table {
            background: var(--bg-card);
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--border);
        }
        
        .listings-header-row {
            background: var(--bg-secondary);
            padding: 1rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 1rem;
            font-weight: 600;
            color: var(--accent-primary);
            border-bottom: 1px solid var(--border);
        }
        
        .listing-row {
            padding: 0.75rem 1.5rem;
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 1rem;
            align-items: center;
            border-bottom: 1px solid var(--border);
            transition: background-color 0.2s;
        }
        
        .listing-row:hover {
            background: rgba(0, 217, 255, 0.05);
        }
        
        .listing-row:last-child {
            border-bottom: none;
        }
        
        .listing-title-compact {
            font-size: 0.9rem;
            color: var(--text-primary);
            line-height: 1.4;
            min-width: 0; /* Allow text to truncate */
        }
        
        .listing-price-compact {
            font-weight: 600;
            color: var(--success);
            font-size: 1rem;
            text-align: right;
            min-width: 60px;
        }
        
        .listing-date-compact {
            color: var(--text-secondary);
            font-size: 0.85rem;
            text-align: right;
            min-width: 80px;
        }
        
        .listing-condition-compact {
            background: var(--bg-primary);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-align: center;
            min-width: 80px;
        }
        
        @media (max-width: 768px) {
            .listings-header-row,
            .listing-row {
                grid-template-columns: 1fr auto;
                gap: 0.5rem;
            }
            
            .listing-date-compact,
            .listing-condition-compact {
                display: none;
            }
            
            .listings-header-row .listing-date-compact,
            .listings-header-row .listing-condition-compact {
                display: none;
            }
        }
    '''
    
    # New compact listings HTML structure
    compact_html = '''
            <div class="listings-table">
                <div class="listings-header-row">
                    <div>Article vendu</div>
                    <div>Prix</div>
                    <div class="listing-date-compact">Date</div>
                    <div class="listing-condition-compact">État</div>
                </div>
                
                {LISTINGS_ROWS_HTML}
            </div>
    '''
    
    # Replace the old listings grid CSS
    template = template.replace(
        '        .listings-grid {',
        f'''        {compact_css}
        
        .listings-grid-old {{'''
    )
    
    # Replace the listings HTML structure
    old_structure = '''            <div class="listings-grid">
                {LISTINGS_HTML}
            </div>'''
    
    template = template.replace(old_structure, compact_html)
    
    # Save updated template
    with open('template-v4-compact.html', 'w', encoding='utf-8') as f:
        f.write(template)
    
    print("   ✅ Created template-v4-compact.html with compact display")
    
    return template

def generate_compact_listing_html(listing):
    """Generate compact listing HTML (single row)"""
    
    # Convert YYYY-MM-DD to DD/MM/YYYY for display
    date_parts = listing['sold_date'].split('-')
    if len(date_parts) == 3:
        display_date = f"{date_parts[2]}/{date_parts[1]}/{date_parts[0]}"
    else:
        display_date = listing['sold_date']
    
    return f'''                <div class="listing-row">
                    <div class="listing-title-compact">{listing['title']}</div>
                    <div class="listing-price-compact">{listing['price']:.0f}€</div>
                    <div class="listing-date-compact">{display_date}</div>
                    <div class="listing-condition-compact">{listing['condition']}</div>
                </div>'''

def update_site_generator_for_compact():
    """Update site generator to use compact display and deduplicated data"""
    
    print("\n⚙️ Updating site generator...")
    
    # Read current update_site.py
    with open('update_site.py', 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Update data source priority
    content = content.replace(
        'scraped_data_consoles_only.json',
        'scraped_data_deduplicated.json'
    )
    
    # Update template path
    content = content.replace(
        'template-v3.html',
        'template-v4-compact.html'
    )
    
    # Update format_listing_html function
    old_function = '''def format_listing_html(listing):
    """Format a single listing as HTML (showing sold price history)"""
    
    # Convert YYYY-MM-DD to DD/MM/YYYY for display
    date_parts = listing['sold_date'].split('-')
    if len(date_parts) == 3:
        display_date = f"{date_parts[2]}/{date_parts[1]}/{date_parts[0]}"
    else:
        display_date = listing['sold_date']
    
    return f"""
                <div class="listing-card">
                    <div class="listing-title">{listing['title']}</div>
                    <div class="listing-meta">
                        <span class="listing-price">{listing['price']:.0f}€</span>
                        <span class="listing-date">{display_date}</span>
                    </div>
                    <span class="listing-condition">{listing['condition']}</span>
                </div>\""""'''
    
    new_function = '''def format_listing_html(listing):
    """Format a single listing as compact HTML row"""
    
    # Convert YYYY-MM-DD to DD/MM/YYYY for display
    date_parts = listing['sold_date'].split('-')
    if len(date_parts) == 3:
        display_date = f"{date_parts[2]}/{date_parts[1]}/{date_parts[0]}"
    else:
        display_date = listing['sold_date']
    
    return f"""                <div class="listing-row">
                    <div class="listing-title-compact">{listing['title']}</div>
                    <div class="listing-price-compact">{listing['price']:.0f}€</div>
                    <div class="listing-date-compact">{display_date}</div>
                    <div class="listing-condition-compact">{listing['condition']}</div>
                </div>\""""'''
    
    content = content.replace(old_function, new_function)
    
    # Update placeholder name
    content = content.replace('{LISTINGS_HTML}', '{LISTINGS_ROWS_HTML}')
    content = content.replace('html = html.replace(\'{LISTINGS_ROWS_HTML}\', listings_html)', 
                            'html = html.replace(\'{LISTINGS_ROWS_HTML}\', listings_html)')
    
    # Save updated generator
    with open('update_site_compact.py', 'w', encoding='utf-8') as f:
        f.write(content)
    
    print("   ✅ Created update_site_compact.py with compact display support")

if __name__ == "__main__":
    # Step 1: Deduplicate data
    deduplicate_data()
    
    # Step 2: Update template for compact display  
    update_template_for_compact_display()
    
    # Step 3: Update site generator
    update_site_generator_for_compact()
    
    print("\n🎯 Next steps:")
    print("   1. Review MANUAL_REVIEW.md to classify items")
    print("   2. Run 'python3 update_site_compact.py' to generate compact site")
    print("   3. Add Game Boy Color specs to variant pages")
    print("   4. Test the new compact display")