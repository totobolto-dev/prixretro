#!/usr/bin/env python3
"""
Scrape current eBay listings (FOR SALE) with images
Uses same simple approach as scraper_ebay.py but with current listing selectors
Saves to current_listings.json
"""

import requests
from bs4 import BeautifulSoup
import json
import time
from urllib.parse import quote_plus

def load_config():
    """Load config with variant search terms"""
    with open('config.json', 'r', encoding='utf-8') as f:
        return json.load(f)

def matches_variant(title, variant_key, variant_config):
    """Check if listing title matches the variant using search terms"""
    title_lower = title.lower()

    # For Pokemon variants, only check for pokemon/pikachu
    if 'pokemon' in variant_key:
        return 'pokemon' in title_lower or 'pikachu' in title_lower

    # Get search terms and keywords from config
    search_terms = variant_config.get('search_terms', [])
    keywords = variant_config.get('keywords', [])

    # Check if ANY of the keywords appear in the title
    for keyword in keywords:
        if keyword.lower() in title_lower:
            return True

    # Define STRICT color mappings - must match ONE of these
    color_map = {
        'atomic-purple': ['atomique', 'atomic purple', 'violet atomique'],
        'violet': ['violet'],  # Must say "violet" specifically, not "violet atomique"
        'jaune': ['jaune', 'yellow'],
        'rouge': ['rouge', 'red'],
        'bleu': ['bleu', 'teal', 'cyan'],
        'vert': ['vert', 'green'],
    }

    # Exclusions - if title contains these, reject
    exclusions = {
        'atomic-purple': [],
        'violet': ['atomique', 'atomic', 'transparent'],  # violet variant should NOT have these
        'jaune': ['transparent', 'atomique'],
        'rouge': ['transparent', 'atomique'],
        'bleu': ['atomique'],
        'vert': ['atomique', 'transparent'],
    }

    # Check exclusions first
    if variant_key in exclusions:
        for exclusion in exclusions[variant_key]:
            if exclusion in title_lower:
                return False

    # Check if this variant has color keywords
    if variant_key in color_map:
        for color in color_map[variant_key]:
            if color in title_lower:
                return True

    # NO FALLBACK - if nothing matched, reject
    return False

def is_valid_image(image_url):
    """Check if image URL is valid (not placeholder/black image)"""
    if not image_url:
        return False

    # Filter out eBay static placeholders (these are truly placeholders)
    if 'ebaystatic.com' in image_url:
        return False

    # Require minimum s-l225 size - filter small thumbnails that are often black/unclear
    # s-l140 is too small and often shows black/unclear images
    if 's-l140' in image_url or 's-l80' in image_url or 's-l60' in image_url:
        return False

    # Require s-l225 or larger (s-l500, s-l640, etc.)
    # Images must have good resolution to be useful
    if 'ebayimg.com' in image_url and 's-l' in image_url:
        # Check if it's at least s-l225
        import re
        match = re.search(r's-l(\d+)', image_url)
        if match:
            size = int(match.group(1))
            if size < 225:  # Minimum 225px
                return False

    return True

def scrape_current_listings(variant_key, variant_config, max_items=15):
    """Scrape active eBay listings for a variant with images"""

    variant_name = variant_config.get('name', variant_config.get('variant_name', variant_key))
    print(f"\n🔍 Scraping current listings for: {variant_name}")

    # Build search URL for items FOR SALE (not sold)
    search_term = "game boy color"
    encoded_term = quote_plus(search_term)

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_sacat=139971&"
        f"_sop=10&"  # Sort by price + shipping (lowest first)
        f"_ipg=50"
        # NO LH_Sold - we want active listings
    )

    # Ultra-realistic headers (same as scraper_ebay.py)
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language': 'fr-FR,fr;q=0.9',
        'Accept-Encoding': 'gzip, deflate, br',
        'DNT': '1',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Cache-Control': 'max-age=0',
        'sec-ch-ua': '"Google Chrome";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
        'sec-ch-ua-mobile': '?0',
        'sec-ch-ua-platform': '"Windows"',
        'Referer': 'https://www.ebay.fr/'
    }

    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()

        print(f"  📊 Response status: {response.status_code}")
        print(f"  📄 Response length: {len(response.text)} chars")

        soup = BeautifulSoup(response.text, 'html.parser')

        listings = []

        # Current listings use .s-card structure (different from sold!)
        cards = soup.select('.s-card.s-card--horizontal')
        print(f"  ✅ Found {len(cards)} cards")

        for card in cards:
            try:
                # Get title
                title_elem = card.select_one('.su-styled-text.primary')
                if not title_elem:
                    continue
                title = title_elem.get_text().strip()

                # Skip "Shop on eBay" headers and empty titles
                if 'Shop on eBay' in title or title == '':
                    continue

                # Only keep gameboy color items
                if 'game boy' not in title.lower() and 'gameboy' not in title.lower():
                    continue

                # CRITICAL: Check if title matches the variant
                if not matches_variant(title, variant_key, variant_config):
                    continue

                # Get URL
                link_elem = card.select_one('.s-card__link')
                if not link_elem:
                    continue
                item_url = link_elem.get('href', '').split('?')[0]

                # Get price (base price WITHOUT shipping - eBay usually shows "XX EUR + livraison")
                price_elem = card.select_one('.s-card__price')
                if not price_elem:
                    continue
                price_text = price_elem.get_text().strip()

                # Parse price - just the first number (base price)
                try:
                    price_clean = price_text.replace('EUR', '').replace(',', '.').strip()
                    # Handle price ranges like "50,00 à 100,00" - take the first price
                    if 'à' in price_clean:
                        price_clean = price_clean.split('à')[0].strip()
                    # Remove any text after the price
                    price_clean = price_clean.split()[0] if price_clean else '0'
                    price = float(price_clean)
                except:
                    continue

                # Get condition (try to find it in card)
                condition = 'Occasion'  # Default
                condition_elems = card.select('.su-styled-text.secondary')
                for elem in condition_elems:
                    text = elem.get_text().strip()
                    if 'neuf' in text.lower() or 'occasion' in text.lower():
                        condition = text
                        break

                # Get image - must be valid (not placeholder)
                img_elem = card.select_one('.su-media img')
                image_url = ''
                if img_elem:
                    image_url = img_elem.get('src', '')
                    # Validate image
                    if not is_valid_image(image_url):
                        continue  # Skip listings with invalid images

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
                print(f"  ⚠️  Error parsing card: {e}")
                continue

        return listings

    except Exception as e:
        print(f"  ❌ Error scraping {variant_name}: {e}")
        return []


def scrape_all_variants():
    """Scrape current listings for all variants"""

    # Load config and scraped_data
    config = load_config()

    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        scraped_data = json.load(f)

    all_current_listings = {}

    # Initialize session by visiting homepage first (like scraper_ebay.py)
    print("🌐 Initializing session with eBay.fr...")
    session = requests.Session()
    try:
        session.get('https://www.ebay.fr', timeout=10)
        time.sleep(2)  # Pause like a real user
        print("✅ Session initialized\n")
    except:
        print("⚠️  Session init failed, continuing anyway...\n")

    for variant_key, variant_data in scraped_data.items():
        # Get variant config from config.json
        variant_config = config['variants'].get(variant_key, {})
        if not variant_config:
            print(f"⚠️  No config found for {variant_key}, skipping...")
            continue

        listings = scrape_current_listings(variant_key, variant_config, max_items=15)

        if listings:
            all_current_listings[variant_key] = {
                'variant_key': variant_key,
                'variant_name': variant_data['variant_name'],
                'listings': listings,
                'count': len(listings)
            }
            time.sleep(5)  # Be extra nice to eBay to avoid rate limiting
        else:
            print(f"  ⚠️  No listings found for {variant_data['variant_name']}")

    # Save to JSON
    output_file = 'current_listings.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(all_current_listings, f, indent=2, ensure_ascii=False)

    print(f"\n✅ Saved current listings to: {output_file}")
    print(f"📊 Total variants with listings: {len(all_current_listings)}")

    return all_current_listings


if __name__ == '__main__':
    scrape_all_variants()
