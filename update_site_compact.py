#!/usr/bin/env python3
"""
PrixRetro - Site Generator from Scraped Data
Generates HTML pages from real eBay data
"""

import json
import os
from datetime import datetime

def load_scraped_data():
    """Load scraped data from JSON file - prioritize ultra-clean data"""
    # Try bulletproof data first, then fallbacks
    if os.path.exists('scraped_data_bulletproof.json'):
        data_file = 'scraped_data_bulletproof.json'
    elif os.path.exists('scraped_data_ultra_clean.json'):
        data_file = 'scraped_data_ultra_clean.json'
    elif os.path.exists('scraped_data_clean.json'):
        data_file = 'scraped_data_clean.json'
    else:
        data_file = 'scraped_data.json'
    
    if not os.path.exists(data_file):
        print(f"❌ Error: {data_file} not found!")
        print("Please run scraper_ebay.py first.")
        return None
    
    print(f"📂 Loading data from: {data_file}")
    with open(data_file, 'r', encoding='utf-8') as f:
        return json.load(f)

def load_config():
    """Load configuration"""
    with open('config.json', 'r', encoding='utf-8') as f:
        return json.load(f)

def load_template():
    """Load HTML template"""
    template_path = '../prixretro-static/template-v4-compact.html'
    
    if not os.path.exists(template_path):
        # Try current directory
        template_path = 'template-v4-compact.html'
    
    with open(template_path, 'r', encoding='utf-8') as f:
        return f.read()

def generate_price_graph(price_history):
    """Generate Chart.js price history graph HTML and JS - compact style like pricecharting.com"""

    if not price_history or len(price_history) < 1:
        # No data at all
        return {
            'html': '''
                <div class="no-graph">
                    <p style="color: var(--text-secondary); text-align: center; padding: 1rem;">
                        Pas assez de données historiques
                    </p>
                </div>
            ''',
            'js': ''
        }

    # Prepare data for Chart.js - sort months chronologically
    months = sorted(price_history.keys())  # Sort YYYY-MM format chronologically
    prices = [price_history[month] for month in months]  # Get prices in sorted order

    # Format months for display (YYYY-MM -> Mois Année)
    month_names_fr = {
        '01': 'Jan', '02': 'Fév', '03': 'Mar', '04': 'Avr',
        '05': 'Mai', '06': 'Juin', '07': 'Juil', '08': 'Août',
        '09': 'Sep', '10': 'Oct', '11': 'Nov', '12': 'Déc'
    }

    formatted_labels = []
    for month in months:
        year, month_num = month.split('-')
        formatted_labels.append(f"{month_names_fr.get(month_num, month_num)} '{year[-2:]}")  # "Jan '24" style

    # Compact HTML - smaller container like pricecharting.com
    html = '''
        <div class="graph-wrapper">
            <canvas id="priceChart"></canvas>
        </div>
    '''

    # Compact chart settings - reduced point sizes, tighter layout
    js = f'''
        const ctx = document.getElementById('priceChart').getContext('2d');
        const priceChart = new Chart(ctx, {{
            type: 'line',
            data: {{
                labels: {formatted_labels},
                datasets: [{{
                    label: 'Prix moyen',
                    data: {prices},
                    borderColor: '#00d9ff',
                    backgroundColor: 'rgba(0, 217, 255, 0.05)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.1,
                    pointRadius: 3,
                    pointBackgroundColor: '#00d9ff',
                    pointBorderColor: '#0f1419',
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBorderWidth: 2
                }}]
            }},
            options: {{
                responsive: true,
                maintainAspectRatio: false,
                layout: {{
                    padding: {{
                        top: 10,
                        right: 10,
                        bottom: 0,
                        left: 0
                    }}
                }},
                plugins: {{
                    legend: {{
                        display: false
                    }},
                    tooltip: {{
                        backgroundColor: '#1a1f2e',
                        titleColor: '#e4e6eb',
                        bodyColor: '#e4e6eb',
                        borderColor: '#00d9ff',
                        borderWidth: 1,
                        padding: 8,
                        displayColors: false,
                        titleFont: {{
                            size: 11
                        }},
                        bodyFont: {{
                            size: 13,
                            weight: 'bold'
                        }},
                        callbacks: {{
                            label: function(context) {{
                                return context.parsed.y + '€';
                            }}
                        }}
                    }}
                }},
                scales: {{
                    x: {{
                        grid: {{
                            display: false,
                            drawBorder: false
                        }},
                        ticks: {{
                            color: '#a0a3a8',
                            font: {{
                                size: 10
                            }},
                            maxRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8
                        }}
                    }},
                    y: {{
                        grid: {{
                            color: '#2a3142',
                            drawBorder: false,
                            lineWidth: 1
                        }},
                        ticks: {{
                            color: '#a0a3a8',
                            font: {{
                                size: 10
                            }},
                            callback: function(value) {{
                                return value + '€';
                            }},
                            maxTicksLimit: 5
                        }},
                        beginAtZero: false
                    }}
                }}
            }}
        }});
    '''

    return {'html': html, 'js': js}


def format_listing_html(listing):
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
                </div>"""

def build_ebay_search_url(variant_key, variant_name, campaign_id, network_id, tracking_id):
    """Build eBay search URL for items currently FOR SALE with affiliate params"""
    from urllib.parse import quote_plus
    
    # Search for items FOR SALE (not sold)
    search_term = f"game boy color {variant_name}"
    encoded_term = quote_plus(search_term)
    
    # Build URL with category filters + affiliate params
    # LH_ItemCondition=3000 = Used items
    # Remove LH_Sold to show items FOR SALE
    base_url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_dcat=139971&"
        f"Mod%C3%A8le=Nintendo%20Game%20Boy%20Color&"
        f"Plateforme=Nintendo%20Game%20Boy%20Color&"
        f"LH_ItemCondition=3000"
    )
    
    # Add eBay Partner Network affiliate params
    affiliate_params = (
        f"&mkcid={tracking_id}"
        f"&mkrid={network_id}"
        f"&campid={campaign_id}"
    )
    
    return base_url + affiliate_params

def generate_variant_page(variant_data, all_variants, config, template, output_dir='output'):
    """Generate HTML page for a specific variant"""
    
    variant_key = variant_data['variant_key']
    variant_name = variant_data['variant_name']
    stats = variant_data['stats']
    listings = variant_data['listings']
    description = variant_data['description']
    
    # Get eBay config
    ebay_config = config['ebay_partner']
    campaign_id = ebay_config['campaign_id']
    network_id = ebay_config['network_id']
    tracking_id = ebay_config['tracking_id']
    
    print(f"  📄 Generating: game-boy-color-{variant_key}.html")
    
    # Build eBay search link for items FOR SALE
    ebay_search_link = build_ebay_search_url(
        variant_key, variant_name, campaign_id, network_id, tracking_id
    )
    
    # Generate price history graph
    price_history = stats.get('price_history', {})
    graph_data = generate_price_graph(price_history)
    
    # Format listings HTML (showing SOLD prices for history)
    if listings:
        listings_html = "\n".join([
            format_listing_html(l) 
            for l in listings  # Show ALL listings, not just 20
        ])
    else:
        listings_html = """
                <div class="no-listings">
                    <h3>⚠️ Données insuffisantes</h3>
                    <p>Moins de 10 ventes trouvées pour cette variante.</p>
                    <p>Les prix affichés peuvent ne pas être représentatifs du marché actuel.</p>
                </div>"""
    
    # Generate related variants (exclude current)
    related_variants = []
    for key in all_variants.keys():
        if key != variant_key:
            name = all_variants[key]['variant_name']
            related_variants.append(
                f'<a href="/game-boy-color-{key}.html" class="related-link">{name}</a>'
            )

    related_html = "\n                    ".join(related_variants[:8])

    # Replace placeholders (Laravel-ready structure)
    product_name = f"Game Boy Color {variant_name}"
    html = template.replace('{VARIANT_NAME}', variant_name)
    html = html.replace('{VARIANT_KEY}', variant_key)
    html = html.replace('{PRODUCT_NAME}', product_name)
    html = html.replace('{PRODUCT_SLUG}', f"game-boy-color-{variant_key}")
    html = html.replace('{PRODUCT_DESCRIPTION}', description)
    html = html.replace('{PRODUCT_CATEGORY}', "Console de jeu portable")
    html = html.replace('{BRAND_NAME}', "Nintendo")
    html = html.replace('{LISTING_COUNT}', str(stats['listing_count']))
    html = html.replace('{AVG_PRICE}', str(stats['avg_price']))
    html = html.replace('{MIN_PRICE}', str(stats['min_price']))
    html = html.replace('{MAX_PRICE}', str(stats['max_price']))
    html = html.replace('{VARIANT_DESCRIPTION}', description)
    html = html.replace('{LISTINGS_ROWS_HTML}', listings_html)
    html = html.replace('{RELATED_VARIANTS}', related_html)
    html = html.replace('{EBAY_SEARCH_LINK}', ebay_search_link)
    html = html.replace('{PRICE_GRAPH_HTML}', graph_data['html'])
    html = html.replace('{PRICE_GRAPH_JS}', graph_data['js'])
    
    # Write output file
    os.makedirs(output_dir, exist_ok=True)
    filename = f"game-boy-color-{variant_key}.html"
    filepath = os.path.join(output_dir, filename)
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(html)
    
    return filepath

def generate_index_page(all_variants, output_dir='output'):
    """Generate homepage with all variants"""

    # Load config for descriptions
    with open('config.json', 'r', encoding='utf-8') as f:
        config = json.load(f)

    # Generate variant cards dynamically
    variant_cards = []
    for variant_key, variant_data in all_variants.items():
        variant_name = variant_data.get('variant_name', variant_key.title())

        # Get description from config or variant_data
        description = variant_data.get('description', '')
        if not description and variant_key in config['variants']:
            description = config['variants'][variant_key].get('description', '')

        # Shorten description for homepage
        short_desc = description.split('.')[0] if description else f"Édition {variant_name}"

        variant_card = f'''
                <a href="/game-boy-color-{variant_key}.html" class="variant-card">
                    <div class="variant-name">{variant_name}</div>
                    <div class="variant-description">{short_desc}</div>
                    <div class="variant-stats">
                        <span class="stat-item">{variant_data['stats']['listing_count']} ventes</span>
                        <span class="stat-item">~{variant_data['stats']['avg_price']}€</span>
                    </div>
                </a>'''
        variant_cards.append(variant_card)

    variant_cards_html = '\n'.join(variant_cards)

    # Use template if exists, otherwise create minimal one
    index_template_path = 'index.html'
    if os.path.exists(index_template_path):
        with open(index_template_path, 'r', encoding='utf-8') as f:
            index_html = f.read()

        # Replace variant cards section
        import re
        # Find the variants section and replace it - match from opening tag to closing tag
        # Use a more specific pattern that matches the complete variants-grid block
        pattern = r'(<div class="variants-grid">\s*)((?:.*?)</div>)(\s*</div>)'

        # Find the position manually since regex is tricky with nested divs
        start_marker = '<div class="variants-grid">'
        start_pos = index_html.find(start_marker)

        if start_pos != -1:
            # Find the corresponding closing </div> for variants-grid
            # We need to find the closing tag after all variant cards
            # Look for </div> followed by </div> (variants-grid close + variants-section close)
            search_start = start_pos + len(start_marker)
            depth = 1
            pos = search_start

            while depth > 0 and pos < len(index_html):
                if index_html[pos:pos+6] == '<div c' or index_html[pos:pos+5] == '<div>':
                    # Opening tag found
                    depth += 1
                    pos += 1
                elif index_html[pos:pos+6] == '</div>':
                    # Closing tag found
                    depth -= 1
                    if depth == 0:
                        # Found the matching closing tag
                        end_pos = pos
                        break
                    pos += 6
                else:
                    pos += 1

            if depth == 0:
                # Successfully found matching tags
                before = index_html[:start_pos + len(start_marker)]
                after = index_html[end_pos:]
                index_html = before + '\n' + variant_cards_html + '\n            ' + after

        with open(os.path.join(output_dir, 'index.html'), 'w', encoding='utf-8') as f:
            f.write(index_html)

        print("  📄 Generated: index.html")
    else:
        print("  ⚠️  index.html template not found, skipping homepage")

def generate_all_pages():
    """Generate all HTML pages from scraped data"""
    
    print("="*60)
    print("🎨 Generating HTML pages from scraped data")
    print("="*60)
    
    # Load data
    print("\n📂 Loading data...")
    scraped_data = load_scraped_data()
    if not scraped_data:
        return False
    
    config = load_config()
    template = load_template()
    
    print(f"✅ Loaded data for {len(scraped_data)} variants")
    
    # Create output directory
    output_dir = 'output'
    os.makedirs(output_dir, exist_ok=True)
    
    # Generate variant pages
    print(f"\n🔨 Generating variant pages...")
    generated_files = []
    
    for variant_key, variant_data in scraped_data.items():
        # Skip variants with no listings
        if not variant_data['listings'] or len(variant_data['listings']) == 0:
            print(f"  ⚠️  Skipping {variant_data['variant_name']} - no listings after filtering")
            continue
            
        filepath = generate_variant_page(
            variant_data, 
            scraped_data, 
            config, 
            template, 
            output_dir
        )
        generated_files.append(filepath)
    
    # Generate homepage
    print(f"\n🏠 Generating homepage...")
    generate_index_page(scraped_data, output_dir)
    
    # Copy .htaccess
    print(f"\n⚙️  Copying .htaccess...")
    htaccess_path = '../prixretro-static/.htaccess'
    if not os.path.exists(htaccess_path):
        htaccess_path = '.htaccess'
    
    if os.path.exists(htaccess_path):
        with open(htaccess_path, 'r', encoding='utf-8') as f:
            htaccess = f.read()
        with open(os.path.join(output_dir, '.htaccess'), 'w', encoding='utf-8') as f:
            f.write(htaccess)
        print("  ✅ Copied .htaccess")
    
    # Summary
    print(f"\n{'='*60}")
    print(f"✅ Generation complete!")
    print(f"{'='*60}")
    print(f"\n📊 Summary:")
    print(f"   • Variant pages: {len(generated_files)}")
    print(f"   • Homepage: 1")
    print(f"   • Total files: {len(generated_files) + 2} (.htaccess + index)")
    
    # Statistics
    total_listings = sum(v['stats']['listing_count'] for v in scraped_data.values())
    avg_price_overall = sum(v['stats']['avg_price'] for v in scraped_data.values()) // len(scraped_data)
    
    print(f"\n💰 Price Statistics:")
    print(f"   • Total listings: {total_listings}")
    print(f"   • Overall avg price: {avg_price_overall}€")
    
    print(f"\n📁 Output location: {output_dir}/")
    print(f"\n🚀 Next step:")
    print(f"   1. Review files in '{output_dir}/' folder")
    print(f"   2. Upload to prixretro.com via FTP")
    print(f"   3. Test the site!")
    
    return True

def main():
    """Main execution"""
    success = generate_all_pages()
    
    if success:
        print("\n" + "="*60)
        print("🎉 All done! Your site is ready to upload!")
        print("="*60)
    else:
        print("\n" + "="*60)
        print("❌ Generation failed. Check errors above.")
        print("="*60)

if __name__ == "__main__":
    main()
