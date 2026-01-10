#!/usr/bin/env python3
"""
Universal eBay.fr scraper for retro gaming consoles.
Scrapes ALL sold items from search results (no individual page visits).

Features:
- Scrapes ALL pages until no more results
- Grabs thumbnail image from search results
- Grabs sold date from search results
- Works for any console (GBC, GBA, DS, PSP, etc.)
- Fast: no individual page visits

Usage:
    python3 scraper_ebay_universal.py "game boy color" gbc
    python3 scraper_ebay_universal.py "game boy advance" gba
    python3 scraper_ebay_universal.py "nintendo ds" nds
    python3 scraper_ebay_universal.py "psp" psp
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import sys
import re
from urllib.parse import quote_plus
from datetime import datetime

def normalize_month_token(token):
    token = token.lower()

    # Fix common mojibake / broken accents
    token = re.sub(r"f.*v", "feb", token)   # fév, févr, f�v → feb
    token = re.sub(r"d.*c", "dec", token)   # déc, d�c → dec
    token = re.sub(r"a.*t", "aug", token)   # août, aôut → aug (optional)

    return token

def parse_french_date(date_text):
    """Parse French eBay date: 'Vendu le 20 déc. 2024'"""
    months_fr = {
        'janv': '01', 'jan': '01',
        'févr': '02', 'fév': '02', 'feb': '02',
        'mars': '03', 'mar': '03',
        'avr': '04', 'apr': '04',
        'mai': '05', 'may': '05',
        'juin': '06', 'jun': '06',
        'juil': '07', 'jul': '07',
        'août': '08', 'aug': '08',
        'sept': '09', 'sep': '09',
        'oct': '10',
        'nov': '11',
        'déc': '12', 'dec': '12'
    }

    # Extract date parts
    parts = date_text.lower().replace('.', '').split()
    day = None
    month = None
    year = None

    for part in parts:
        part = normalize_month_token(part)

        # Day (1-31)
        if part.isdigit() and 1 <= int(part) <= 31 and day is None:
            day = part.zfill(2)
        # Month
        elif part in months_fr:
            month = months_fr[part]
        # Year (4 digits)
        elif part.isdigit() and len(part) == 4:
            year = part

    if day and month and year:
        return f"{year}-{month}-{day}"

    # If no year found, assume current year
    if day and month and not year:
        current_year = datetime.now().year
        current_month = datetime.now().month
        # If month is in future, it was from last year
        if int(month) > current_month:
            year = str(current_year - 1)
        else:
            year = str(current_year)
        return f"{year}-{month}-{day}"

    return None

def scrape_ebay_console(search_term, console_slug, max_pages=50):
    """
    Scrape all sold items for a console from eBay.fr

    Args:
        search_term: Search query (e.g., "game boy color")
        console_slug: Short name for output file (e.g., "gbc")
        max_pages: Maximum pages to scrape (default 50, stops earlier if no more results)

    Returns:
        List of items scraped
    """

    print("="*70)
    print(f"🎮 SCRAPING EBAY.FR: {search_term.upper()}")
    print("="*70)
    print()

    encoded_term = quote_plus(search_term)
    all_items = []
    seen_ids = set()

    # Load rejected item IDs to skip
    rejected_ids_file = 'scrapers/rejected_item_ids.json'
    try:
        with open(rejected_ids_file, 'r') as f:
            rejected_ids = json.load(f)
            seen_ids.update(rejected_ids)
            print(f"📋 Loaded {len(rejected_ids)} rejected item IDs to skip")
            print()
    except FileNotFoundError:
        print("ℹ️  No rejected items file found (first run)")
        print()

    # Browser headers
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

    # Initialize session
    print("🌐 Initializing session...")
    session = requests.Session()
    try:
        session.get('https://www.ebay.fr', headers=headers, timeout=10)
        time.sleep(2)
        session.get('https://www.ebay.fr/b/Consoles-de-jeux-video/139971/bn_1865037', headers=headers, timeout=10)
        time.sleep(2)
        print("✅ Session ready\n")
    except Exception as e:
        print(f"⚠️  Session init warning: {e}\n")

    # Scrape pages
    for page_num in range(1, max_pages + 1):
        print(f"📄 Page {page_num}")

        # eBay URL with sold filter and console category
        url = (
            f"https://www.ebay.fr/sch/i.html?"
            f"_nkw={encoded_term}&"
            f"_sacat=139971&"  # Category: Consoles de jeux vidéo
            f"LH_Sold=1&"
            f"_sop=10&"
            f"_ipg=240&"
            f"_pgn={page_num}"
        )

        try:
            response = session.get(url, headers=headers, timeout=15)
            response.raise_for_status()
            response.encoding = 'utf-8'  # Force UTF-8 to handle French characters (é, à, etc.)

            soup = BeautifulSoup(response.text, 'html.parser')

            # Find sold listing cards
            # Try multiple selectors (eBay changes layouts)
            items = soup.select('.s-card.s-card--horizontal')

            if len(items) == 0:
                # Try alternative selector for .s-item layout
                items = soup.select('li.s-item')
                print(f"  Found {len(items)} items (.s-item selector)")
            else:
                print(f"  Found {len(items)} cards (.s-card selector)")

            if len(items) == 0:
                # Debug: check what's on the page
                print(f"  ⚠️  No items found with either selector")
                if page_num == 1:
                    # Save HTML for debugging
                    with open(f'debug_ebay_{console_slug}_p1.html', 'w', encoding='utf-8') as f:
                        f.write(response.text)
                    print(f"  💾 Saved HTML to debug_ebay_{console_slug}_p1.html")
                break

            page_items = 0

            for item in items:
                try:
                    # Title
                    title_elem = item.select_one('.su-styled-text.primary')
                    if not title_elem:
                        continue

                    title = title_elem.get_text().strip()

                    if 'Shop on eBay' in title or title == '':
                        continue

                    # Filter by search term (basic matching)
                    title_lower = title.lower()
                    search_keywords = search_term.lower().split()
                    if not any(keyword in title_lower for keyword in search_keywords):
                        continue

                    # URL and item ID
                    link_elem = item.select_one('.s-card__link')
                    if not link_elem:
                        continue

                    url = link_elem.get('href', '')

                    # Extract item ID
                    try:
                        item_id = url.split('/itm/')[1].split('?')[0] if '/itm/' in url else None
                    except:
                        item_id = None

                    if not item_id or item_id in seen_ids:
                        continue

                    seen_ids.add(item_id)

                    # Price
                    price_elem = item.select_one('.s-card__price')
                    if not price_elem:
                        continue

                    price_text = price_elem.get_text().strip()

                    try:
                        price_clean = price_text.replace('EUR', '').replace(',', '.').strip()
                        if 'à' in price_clean:
                            price_clean = price_clean.split('à')[0].strip()
                        price_clean = price_clean.split()[0] if price_clean else '0'
                        price = float(price_clean)
                    except:
                        continue

                    if price <= 0:
                        continue

                    # Sold date from search results
                    sold_date = None

                    date_elem = item.select_one('.su-styled-text.positive')
                    if date_elem:
                        date_text = date_elem.get_text().strip()
                        sold_date = parse_french_date(date_text)

                    if not sold_date:
                        sold_date = datetime.now().strftime('%Y-%m-%d')

                    # Thumbnail image from search results
                    thumbnail_url = None
                    img_elem = item.select_one('img')
                    if img_elem:
                        img_src = img_elem.get('src', '')
                        # eBay uses s-l140 or s-l225 for thumbnails, upgrade to s-l500
                        if 's-l' in img_src:
                            thumbnail_url = img_src.replace('s-l140', 's-l500').replace('s-l225', 's-l500')
                        elif img_src.startswith('http'):
                            thumbnail_url = img_src

                    # Condition
                    condition = 'Occasion'
                    condition_elems = item.select('.su-styled-text.secondary')
                    for elem in condition_elems:
                        text = elem.get_text().strip()
                        text_lower = text.lower()
                        # Look for condition keywords
                        if any(word in text_lower for word in ['neuf', 'occasion', 'reconditionné', 'pour pièces', 'ne fonctionne pas']):
                            condition = text
                            break

                    # Add item
                    item_data = {
                        'item_id': item_id,
                        'title': title,
                        'price': price,
                        'sold_date': sold_date,
                        'condition': condition,
                        'url': url.split('?')[0] if '?' in url else url  # Clean URL
                    }

                    if thumbnail_url:
                        item_data['thumbnail_url'] = thumbnail_url

                    all_items.append(item_data)
                    page_items += 1

                except Exception as e:
                    continue

            print(f"  ✅ Extracted {page_items} items (total: {len(all_items)})")

            # Stop if few items (reached end of results)
            if page_items < 50:
                print(f"  🏁 Last page reached (only {page_items} items)")
                break

            # Rate limiting
            time.sleep(3)

        except Exception as e:
            print(f"  ❌ Error on page {page_num}: {e}")
            break

    # Save results
    import os
    # Save to Laravel storage/app directory
    storage_dir = os.path.join(os.path.dirname(__file__), '..', 'storage', 'app')
    os.makedirs(storage_dir, exist_ok=True)
    output_file = os.path.join(storage_dir, f"scraped_data_{console_slug}.json")

    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(all_items, f, indent=2, ensure_ascii=False)

    print(f"\n{'='*70}")
    print(f"✅ SCRAPING COMPLETE")
    print(f"{'='*70}")
    print(f"Total items: {len(all_items)}")
    print(f"Pages scraped: {page_num}")
    print(f"Saved to: {output_file}")
    print()

    return all_items

if __name__ == '__main__':
    import argparse

    parser = argparse.ArgumentParser(description='Scrape eBay.fr sold listings')
    parser.add_argument('search_term', help='Search term (e.g., "game boy color")')
    parser.add_argument('console_slug', help='Console slug for output file (e.g., gbc, gba, nds)')
    parser.add_argument('--max-pages', type=int, default=50, help='Maximum pages to scrape (default: 50)')

    args = parser.parse_args()

    scrape_ebay_console(args.search_term, args.console_slug, args.max_pages)
