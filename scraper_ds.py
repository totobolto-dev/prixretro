#!/usr/bin/env python3
"""
Scrape ALL Nintendo DS sold items from eBay.fr
Saves raw data to scraped_data_ds_raw.json for manual categorization

Focus: DS Lite (most popular), DSi, DSi XL

Usage: python3 scraper_ds.py
"""

import requests
from bs4 import BeautifulSoup
import json
import time
from urllib.parse import quote_plus
from datetime import datetime

def scrape_ds_sold_items(max_pages=10):
    """Scrape all Nintendo DS sold items (last 3 months)"""

    print("="*70)
    print("🎮 SCRAPING NINTENDO DS SOLD ITEMS")
    print("="*70)
    print()

    # Search term - broad to catch all DS variants
    search_term = "nintendo ds"
    encoded_term = quote_plus(search_term)

    all_items = []
    seen_ids = set()

    # Headers (same as working GBA scraper)
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
    print("🌐 Initializing session with eBay.fr...")
    session = requests.Session()
    try:
        session.get('https://www.ebay.fr', headers=headers, timeout=10)
        time.sleep(3)
        session.get('https://www.ebay.fr/b/Consoles-de-jeux-video/139971/bn_1865037', headers=headers, timeout=10)
        time.sleep(3)
        print("✅ Session initialized\n")
    except Exception as e:
        print(f"⚠️  Session init failed: {e}, continuing anyway...\n")

    # Scrape multiple pages
    for page_num in range(1, max_pages + 1):
        print(f"📄 Page {page_num}/{max_pages}")

        # Build URL
        url = (
            f"https://www.ebay.fr/sch/i.html?"
            f"_nkw={encoded_term}&"
            f"LH_Sold=1&"
            f"LH_Complete=1&"
            f"_sop=10&"
            f"_ipg=240&"
            f"_pgn={page_num}"
        )

        try:
            response = session.get(url, headers=headers, timeout=15)
            response.raise_for_status()

            soup = BeautifulSoup(response.text, 'html.parser')

            # Find items (sold listings use .s-card)
            items = soup.select('.s-card.s-card--horizontal')
            print(f"  Found {len(items)} cards on page")

            page_items = 0

            for item in items:
                try:
                    # Get title
                    title_elem = item.select_one('.su-styled-text.primary')
                    if not title_elem:
                        continue

                    title = title_elem.get_text().strip()

                    # Skip headers
                    if 'Shop on eBay' in title or title == '':
                        continue

                    # Only keep Nintendo DS items
                    title_lower = title.lower()
                    if 'nintendo ds' not in title_lower and 'ds lite' not in title_lower and 'dsi' not in title_lower:
                        continue

                    # Reject 3DS/2DS (different console generation)
                    if '3ds' in title_lower or '2ds' in title_lower:
                        continue

                    # Get URL and item ID
                    link_elem = item.select_one('.s-card__link')
                    if not link_elem:
                        continue

                    url = link_elem.get('href', '')
                    try:
                        item_id = url.split('/itm/')[1].split('?')[0] if '/itm/' in url else None
                    except:
                        item_id = None

                    if not item_id:
                        continue

                    # Skip duplicates
                    if item_id in seen_ids:
                        continue

                    seen_ids.add(item_id)

                    # Get price
                    price_elem = item.select_one('.s-card__price')
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

                    # Get sold date
                    date_elem = item.select_one('.su-styled-text.POSITIVE')
                    sold_date = ''
                    if date_elem:
                        date_text = date_elem.get_text().strip()
                        try:
                            # French month names
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

                            for i, part in enumerate(parts):
                                if part.isdigit() and 1 <= int(part) <= 31 and day is None:
                                    day = part.zfill(2)
                                elif part in months_fr:
                                    month = months_fr[part]
                                elif part.isdigit() and len(part) == 4:
                                    year = part

                            if day and month and year:
                                sold_date = f"{year}-{month}-{day}"
                        except:
                            pass

                    if not sold_date:
                        sold_date = datetime.now().strftime('%Y-%m-%d')

                    # Get condition
                    condition = 'Occasion'
                    condition_elems = item.select('.su-styled-text.secondary')
                    for elem in condition_elems:
                        text = elem.get_text().strip()
                        if 'neuf' in text.lower() or 'occasion' in text.lower():
                            condition = text
                            break

                    # Add item
                    all_items.append({
                        'item_id': item_id,
                        'title': title,
                        'price': price,
                        'sold_date': sold_date,
                        'condition': condition,
                        'url': url.split('?')[0]
                    })

                    page_items += 1

                except Exception as e:
                    print(f"  ⚠️  Error parsing item: {e}")
                    continue

            print(f"  ✅ Extracted {page_items} valid DS items")
            print(f"  📊 Total unique items so far: {len(all_items)}\n")

            # If few items, probably reached the end
            if page_items < 50:
                print("⚠️  Few items on this page, likely reached the end of results")
                break

            # Be nice to eBay
            time.sleep(3)

        except Exception as e:
            print(f"  ❌ Error on page {page_num}: {e}\n")
            break

    print("="*70)
    print("📊 SCRAPING COMPLETE")
    print("="*70)
    print(f"Total unique DS items found: {len(all_items)}")
    print()

    # Save raw data
    output_file = 'scraped_data_ds_raw.json'

    output_data = {
        'raw_items': all_items,
        'metadata': {
            'total_items': len(all_items),
            'scrape_date': datetime.now().isoformat(),
            'search_term': search_term,
            'console_type': 'nintendo-ds'
        }
    }

    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(output_data, f, indent=2, ensure_ascii=False)

    print(f"✅ Saved to: {output_file}")
    print()
    print("🎯 NEXT STEPS:")
    print("1. Run: python3 create_ds_variant_sorter.py")
    print("2. Open variant_sorter_ds.html in browser")
    print("3. Sort items into variants (keyboard shortcuts: K/B/P/R)")
    print("4. Export sorted JSON when done")
    print()

    return all_items

if __name__ == '__main__':
    scrape_ds_sold_items(max_pages=10)
