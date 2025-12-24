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

def generate_price_graph(listings):
    """Generate Chart.js price history graph HTML and JS - showing individual sales with clickable points"""

    if not listings or len(listings) < 1:
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

    # Sort listings by date (oldest first for graph)
    sorted_listings = sorted(listings, key=lambda x: x['sold_date'])

    # Format dates for display
    month_names_fr = {
        '01': 'Jan', '02': 'Fév', '03': 'Mar', '04': 'Avr',
        '05': 'Mai', '06': 'Juin', '07': 'Juil', '08': 'Août',
        '09': 'Sep', '10': 'Oct', '11': 'Nov', '12': 'Déc'
    }

    # Prepare data for each point
    labels = []
    prices = []
    urls = []
    titles = []

    for listing in sorted_listings:
        if listing['price'] > 0:
            # Format date for label
            date_parts = listing['sold_date'].split('-')
            if len(date_parts) == 3:
                year, month, day = date_parts
                label = f"{day} {month_names_fr.get(month, month)}"
                labels.append(label)
            else:
                labels.append(listing['sold_date'])

            prices.append(listing['price'])
            urls.append(listing.get('url', ''))
            titles.append(listing.get('title', '').replace("'", "\\'").replace('"', '\\"'))

    html = '''
        <div class="graph-wrapper">
            <canvas id="priceChart"></canvas>
        </div>
    '''

    # Convert to JSON-safe format
    import json
    labels_json = json.dumps(labels)
    urls_json = json.dumps(urls)
    titles_json = json.dumps(titles)

    js = f'''
        const ctx = document.getElementById('priceChart').getContext('2d');
        const chartData = {{
            labels: {labels_json},
            prices: {prices},
            urls: {urls_json},
            titles: {titles_json}
        }};

        const priceChart = new Chart(ctx, {{
            type: 'line',
            data: {{
                labels: chartData.labels,
                datasets: [{{
                    label: 'Prix de vente',
                    data: chartData.prices,
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0, 255, 136, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointBackgroundColor: '#00ff88',
                    pointBorderColor: '#0f1419',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7,
                    pointHoverBorderWidth: 3,
                    pointHoverBackgroundColor: '#00ff88',
                    pointHoverBorderColor: '#00d9ff'
                }}]
            }},
            options: {{
                responsive: true,
                maintainAspectRatio: false,
                interaction: {{
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }},
                onClick: (event, activeElements) => {{
                    if (activeElements.length > 0) {{
                        const index = activeElements[0].index;
                        const url = chartData.urls[index];
                        if (url) {{
                            window.open(url, '_blank', 'noopener,noreferrer');
                        }}
                    }}
                }},
                layout: {{
                    padding: {{
                        top: 15,
                        right: 15,
                        bottom: 5,
                        left: 5
                    }}
                }},
                plugins: {{
                    legend: {{
                        display: false
                    }},
                    tooltip: {{
                        backgroundColor: '#1a1f29',
                        titleColor: '#ffffff',
                        bodyColor: '#00ff88',
                        borderColor: '#2a2f39',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        titleFont: {{
                            size: 11,
                            weight: 'normal'
                        }},
                        bodyFont: {{
                            size: 14,
                            weight: '600'
                        }},
                        callbacks: {{
                            title: function(context) {{
                                const index = context[0].dataIndex;
                                const title = chartData.titles[index];
                                return title.length > 50 ? title.substring(0, 50) + '...' : title;
                            }},
                            label: function(context) {{
                                return context.parsed.y + '€';
                            }},
                            afterLabel: function(context) {{
                                const index = context[0].dataIndex;
                                return chartData.labels[index] + ' • Cliquer pour voir';
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
                            color: '#6b7280',
                            font: {{
                                size: 11
                            }},
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 12
                        }}
                    }},
                    y: {{
                        grid: {{
                            color: '#2a2f39',
                            drawBorder: false,
                            lineWidth: 1
                        }},
                        ticks: {{
                            color: '#6b7280',
                            font: {{
                                size: 11
                            }},
                            callback: function(value) {{
                                return value + '€';
                            }},
                            maxTicksLimit: 6
                        }},
                        beginAtZero: false
                    }}
                }}
            }}
        }});
    '''

    return {'html': html, 'js': js}


def format_listing_html(listing):
    """Format a single listing as compact table row - clickable to eBay"""

    # Convert YYYY-MM-DD to DD/MM/YYYY for display
    date_parts = listing['sold_date'].split('-')
    if len(date_parts) == 3:
        display_date = f"{date_parts[2]}/{date_parts[1]}/{date_parts[0]}"
    else:
        display_date = listing['sold_date']

    # Get eBay URL and source
    ebay_url = listing.get('url', '#')
    source = listing.get('source', 'eBay')

    return f"""
                <a href="{ebay_url}" class="listing-row" target="_blank" rel="nofollow noopener">
                    <div class="listing-title-compact">{listing['title']}</div>
                    <div class="listing-price-compact">{listing['price']:.0f}€</div>
                    <div class="listing-date-compact">{display_date}</div>
                    <div class="listing-source-compact">{source}</div>
                    <div class="listing-condition-compact">{listing['condition']}</div>
                </a>"""

def format_current_listing_card(listing):
    """Format a current listing card with image"""

    url = listing.get('url', '#')
    title = listing['title']
    price = listing['price']
    condition = listing.get('condition', 'Occasion')
    image_url = listing.get('image_url', '')

    return f"""
                <a href="{url}" class="current-listing-card" target="_blank" rel="nofollow noopener">
                    <img src="{image_url}" alt="{title}" class="current-listing-image" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 200%22%3E%3Crect fill=%22%231a1f29%22 width=%22200%22 height=%22200%22/%3E%3C/svg%3E'">
                    <div class="current-listing-content">
                        <div class="current-listing-badge">EN VENTE</div>
                        <div class="current-listing-title">{title}</div>
                        <div class="current-listing-meta">
                            <span class="current-listing-price">{price:.0f}€</span>
                            <span class="current-listing-condition">{condition}</span>
                        </div>
                    </div>
                </a>"""

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

def generate_variant_page(variant_data, all_variants, config, template, output_dir='output', current_listings_data=None):
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

    # Load current listings for this variant
    current_listings_html = ''
    current_listings_count = 0
    if current_listings_data and variant_key in current_listings_data:
        all_current_listings = current_listings_data[variant_key]['listings']

        # Filter: only show listings within ±30% of average price
        avg_price = stats['avg_price']
        min_acceptable = avg_price * 0.7  # -30%
        max_acceptable = avg_price * 1.3  # +30%

        filtered_listings = [
            l for l in all_current_listings
            if min_acceptable <= l['price'] <= max_acceptable
        ]

        if filtered_listings:
            current_listings_count = len(filtered_listings)
            current_listings_html = "\n".join([
                format_current_listing_card(l)
                for l in filtered_listings
            ])
            print(f"    📊 Current listings: {len(filtered_listings)}/{len(all_current_listings)} within ±30% of {avg_price}€ (filtered {len(all_current_listings) - len(filtered_listings)} outliers)")
        else:
            current_listings_html = '<p style="text-align: center; color: var(--text-secondary); padding: 2rem;">Aucune offre dans la fourchette de prix acceptable pour le moment</p>'
    else:
        current_listings_html = '<p style="text-align: center; color: var(--text-secondary); padding: 2rem;">Aucune offre disponible pour le moment</p>'

    # Generate price history graph - pass listings to show individual sales
    graph_data = generate_price_graph(listings)
    
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
    html = html.replace('{CURRENT_LISTINGS_HTML}', current_listings_html)
    html = html.replace('{CURRENT_LISTINGS_COUNT}', str(current_listings_count))
    
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

    # Load current listings if available
    current_listings_data = None
    if os.path.exists('current_listings.json'):
        with open('current_listings.json', 'r', encoding='utf-8') as f:
            current_listings_data = json.load(f)
        print(f"✅ Loaded current listings for {len(current_listings_data)} variants")
    else:
        print("⚠️  No current_listings.json found, skipping current listings")

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
            output_dir,
            current_listings_data
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

    # Copy CSS file
    print(f"\n📄 Copying styles.css...")
    css_path = 'styles.css'
    if os.path.exists(css_path):
        with open(css_path, 'r', encoding='utf-8') as f:
            css_content = f.read()
        with open(os.path.join(output_dir, 'styles.css'), 'w', encoding='utf-8') as f:
            f.write(css_content)
        print("  ✅ Copied styles.css")
    else:
        print("  ⚠️  styles.css not found!")
    
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
