#!/usr/bin/env python3
"""
Scrape current eBay listings (FOR SALE) with images
Saves to current_listings.json
"""

import requests
from bs4 import BeautifulSoup
import json
import time
from urllib.parse import quote_plus

def scrape_current_listings(variant_key, variant_name, max_items=5):
    """Scrape active eBay listings for a variant with images"""

    print(f"\n🔍 Scraping current listings for: {variant_name}")

    # Build search URL for items FOR SALE (not sold)
    search_term = f"game boy color {variant_name}"
    encoded_term = quote_plus(search_term)

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_sacat=139971&"
        f"_sop=10&"  # Sort by price + shipping (lowest first)
        f"_ipg=50"
        # NO LH_Sold - we want active listings
    )

    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    }

    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        soup = BeautifulSoup(response.content, 'html.parser')

        listings = []
        items = soup.select('.s-item')

        for item in items[:max_items]:
            try:
                # Skip sponsored/ads
                if 'SPONSORISÉ' in item.get_text():
                    continue

                # Get title
                title_elem = item.select_one('.s-item__title')
                if not title_elem:
                    continue
                title = title_elem.get_text().strip()

                # Skip "Shop on eBay" header
                if 'Shop on eBay' in title or title == '':
                    continue

                # Get URL
                link_elem = item.select_one('.s-item__link')
                if not link_elem:
                    continue
                item_url = link_elem['href'].split('?')[0]  # Clean URL

                # Get price
                price_elem = item.select_one('.s-item__price')
                if not price_elem:
                    continue
                price_text = price_elem.get_text().strip()

                # Parse price (handle "XX,XX EUR" or "XX EUR")
                try:
                    price_clean = price_text.replace('EUR', '').replace(',', '.').strip()
                    # Handle price ranges like "50,00 à 100,00"
                    if 'à' in price_clean:
                        price_clean = price_clean.split('à')[0].strip()
                    price = float(price_clean)
                except:
                    continue

                # Get condition
                condition_elem = item.select_one('.SECONDARY_INFO')
                condition = condition_elem.get_text().strip() if condition_elem else 'Occasion'

                # Get image
                img_elem = item.select_one('.s-item__image-img')
                image_url = ''
                if img_elem and img_elem.get('src'):
                    image_url = img_elem['src']

                # Only keep gameboy color items
                if 'game boy' not in title.lower() and 'gameboy' not in title.lower():
                    continue

                listings.append({
                    'title': title,
                    'url': item_url,
                    'price': price,
                    'condition': condition,
                    'image_url': image_url
                })

                print(f"  ✅ {title[:60]} - {price}€")

                if len(listings) >= max_items:
                    break

            except Exception as e:
                print(f"  ⚠️  Error parsing item: {e}")
                continue

        return listings

    except Exception as e:
        print(f"  ❌ Error scraping {variant_name}: {e}")
        return []


def scrape_all_variants():
    """Scrape current listings for all variants"""

    # Load config
    with open('config.json', 'r', encoding='utf-8') as f:
        config = json.load(f)

    all_current_listings = {}

    for variant_key, variant_info in config['variants'].items():
        variant_name = variant_info['name']

        listings = scrape_current_listings(variant_key, variant_name, max_items=5)

        if listings:
            all_current_listings[variant_key] = {
                'variant_key': variant_key,
                'variant_name': variant_name,
                'listings': listings,
                'count': len(listings)
            }
            time.sleep(2)  # Be nice to eBay
        else:
            print(f"  ⚠️  No listings found for {variant_name}")

    # Save to JSON
    output_file = 'current_listings.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(all_current_listings, f, indent=2, ensure_ascii=False)

    print(f"\n✅ Saved current listings to: {output_file}")
    print(f"📊 Total variants with listings: {len(all_current_listings)}")

    return all_current_listings


if __name__ == '__main__':
    scrape_all_variants()
