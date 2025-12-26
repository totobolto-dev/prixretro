#!/usr/bin/env python3
"""
Scrape current GBA listings (FOR SALE) with images
Saves to current_listings_gba.json
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import os
from urllib.parse import quote_plus

def load_gba_variants():
    """Load GBA variant config"""
    # Try multi-console config first
    if os.path.exists('config_multiconsole.json'):
        with open('config_multiconsole.json', 'r', encoding='utf-8') as f:
            config = json.load(f)
        return config['consoles']['game-boy-advance']['variants']
    elif os.path.exists('scraped_data_gba.json'):
        # Extract variants from scraped data
        with open('scraped_data_gba.json', 'r', encoding='utf-8') as f:
            data = json.load(f)
        variants = {}
        for key, variant_data in data.items():
            variants[key] = {
                'name': variant_data['variant_name'],
                'description': variant_data.get('description', '')
            }
        return variants
    else:
        print("⚠️  No GBA config found, using default variants")
        # Default GBA variants
        return {
            'sp-platinum': {'name': 'SP Platinum'},
            'sp-cobalt': {'name': 'SP Cobalt'},
            'sp-graphite': {'name': 'SP Graphite'},
            'standard-purple': {'name': 'Standard Purple'}
        }

def matches_gba_variant(title, variant_key):
    """Check if listing title matches the GBA variant"""
    title_lower = title.lower()

    # Must contain "game boy advance", "gameboy advance", or "gba"
    if not ('game boy advance' in title_lower or 'gameboy advance' in title_lower or 'gba' in title_lower):
        return False

    # Variant matching logic for GBA
    variant_keywords = {
        # Standard GBA
        'standard-purple': ['purple', 'violet', 'mauve'],
        'standard-black': ['black', 'noir'],
        'standard-glacier': ['glacier', 'blue transparent', 'bleu transparent'],
        'standard-orange': ['orange'],
        'standard-pink': ['pink', 'rose'],

        # SP variants
        'sp-platinum': ['platinum', 'platine', 'silver', 'argent'],
        'sp-cobalt': ['cobalt', 'blue', 'bleu'],
        'sp-flame': ['flame', 'flamme', 'red', 'rouge'],
        'sp-graphite': ['graphite', 'gray', 'grey', 'gris'],
        'sp-pearl-blue': ['pearl blue', 'bleu nacré'],
        'sp-pearl-pink': ['pearl pink', 'rose nacré'],
        'sp-tribal-edition': ['tribal'],
        'sp-famicom': ['famicom'],
        'sp-nes': ['nes edition'],

        # Micro variants
        'micro-silver': ['micro', 'silver', 'argent'],
        'micro-black': ['micro', 'black', 'noir'],
        'micro-blue': ['micro', 'blue', 'bleu'],
        'micro-pink': ['micro', 'pink', 'rose'],
        'micro-famicom': ['micro', 'famicom']
    }

    # Check if variant is SP
    if variant_key.startswith('sp-'):
        # Must mention SP or "advance sp"
        if 'sp' not in title_lower:
            return False

        # Get keywords for this variant
        keywords = variant_keywords.get(variant_key, [])
        for keyword in keywords:
            if keyword in title_lower and keyword != 'sp':
                return True
        return False

    # Check if variant is Micro
    if variant_key.startswith('micro-'):
        # Must mention "micro"
        if 'micro' not in title_lower:
            return False

        # Get keywords for this variant
        keywords = variant_keywords.get(variant_key, [])
        for keyword in keywords:
            if keyword in title_lower and keyword != 'micro':
                return True
        return 'micro' in title_lower  # At least match "micro"

    # Standard GBA variants
    if variant_key.startswith('standard-'):
        # Reject if it's SP or Micro
        if 'sp' in title_lower or 'micro' in title_lower:
            return False

        # Get keywords for this variant
        keywords = variant_keywords.get(variant_key, [])
        for keyword in keywords:
            if keyword in title_lower:
                return True

    return False

def is_valid_image(image_url):
    """Check if image URL is valid (not placeholder/black image)"""
    if not image_url:
        return False

    # Filter out eBay static placeholders
    if 'ebaystatic.com' in image_url and '/rs/' in image_url:
        return False

    # Only reject tiny thumbnails
    if 's-l80' in image_url or 's-l60' in image_url or 's-l40' in image_url:
        return False

    return True

def scrape_gba_current_listings(variant_key, variant_config, max_items=15):
    """Scrape active eBay listings for a GBA variant with images"""

    variant_name = variant_config.get('name', variant_key.replace('-', ' ').title())
    print(f"\n🔍 Scraping current GBA listings for: {variant_name}")

    # Build search URL for GBA items FOR SALE
    search_term = "game boy advance"
    encoded_term = quote_plus(search_term)

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_sacat=139971&"
        f"_sop=10&"  # Sort by price + shipping (lowest first)
        f"_ipg=50"
        # NO LH_Sold - we want active listings
    )

    # Ultra-realistic headers
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

        soup = BeautifulSoup(response.text, 'html.parser')

        listings = []

        # Current listings use .s-card structure
        cards = soup.select('.s-card.s-card--horizontal')
        print(f"  ✅ Found {len(cards)} GBA cards")

        for card in cards:
            try:
                # Get title
                title_elem = card.select_one('.su-styled-text.primary')
                if not title_elem:
                    continue
                title = title_elem.get_text().strip()

                # Skip headers and empty titles
                if 'Shop on eBay' in title or title == '':
                    continue

                # CRITICAL: Check if title matches the variant
                if not matches_gba_variant(title, variant_key):
                    continue

                # Get URL
                link_elem = card.select_one('.s-card__link')
                if not link_elem:
                    continue
                item_url = link_elem.get('href', '').split('?')[0]

                # Get price
                price_elem = card.select_one('.s-card__price')
                if not price_elem:
                    continue
                price_text = price_elem.get_text().strip()

                # Parse price
                try:
                    price_clean = price_text.replace('EUR', '').replace(',', '.').strip()
                    if 'à' in price_clean:
                        price_clean = price_clean.split('à')[0].strip()
                    price_clean = price_clean.split()[0] if price_clean else '0'
                    price = float(price_clean)
                except:
                    continue

                # Get condition
                condition = 'Occasion'
                condition_elems = card.select('.su-styled-text.secondary')
                for elem in condition_elems:
                    text = elem.get_text().strip()
                    if 'neuf' in text.lower() or 'occasion' in text.lower():
                        condition = text
                        break

                # Get image - must be valid
                img_elem = card.select_one('.su-media img')
                image_url = ''
                if img_elem:
                    image_url = img_elem.get('src', '')
                    # Validate image
                    if not is_valid_image(image_url):
                        image_url = ''  # Clear invalid image

                # Add listing
                listings.append({
                    'title': title,
                    'price': price,
                    'condition': condition,
                    'url': item_url,
                    'image_url': image_url
                })

                # Stop at max_items
                if len(listings) >= max_items:
                    break

            except Exception as e:
                continue

        print(f"  ✅ Found {len(listings)} matching listings for {variant_name}")
        return listings

    except Exception as e:
        print(f"  ❌ Error scraping {variant_name}: {e}")
        return []

def scrape_all_gba_variants():
    """Scrape current listings for all GBA variants"""

    print("="*70)
    print("🎮 SCRAPING GBA CURRENT LISTINGS")
    print("="*70)

    variants = load_gba_variants()
    print(f"\n📊 Loaded {len(variants)} GBA variants")

    all_listings = {}

    for variant_key, variant_config in variants.items():
        listings = scrape_gba_current_listings(variant_key, variant_config, max_items=15)

        if listings:
            all_listings[variant_key] = {
                'variant_name': variant_config.get('name', variant_key),
                'listings': listings
            }

        # Be nice to eBay
        time.sleep(2)

    # Save to JSON
    output_file = 'current_listings_gba.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(all_listings, f, indent=2, ensure_ascii=False)

    print()
    print("="*70)
    print("✅ SCRAPING COMPLETE")
    print("="*70)
    print()
    print(f"📊 Summary:")
    total_listings = sum(len(v['listings']) for v in all_listings.values())
    print(f"   • Variants scraped: {len(all_listings)}")
    print(f"   • Total listings: {total_listings}")
    print()
    print(f"💾 Saved to: {output_file}")
    print()

    # Show variant breakdown
    for variant_key, data in all_listings.items():
        count = len(data['listings'])
        print(f"   • {data['variant_name']}: {count} listings")

    print()
    print("📋 Next step:")
    print("   Run: python3 update_site_compact.py")
    print()

if __name__ == '__main__':
    scrape_all_gba_variants()
