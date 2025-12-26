#!/usr/bin/env python3
"""
Scrape ALL Game Boy Advance sold items from eBay.fr
Saves raw data to scraped_data_gba_raw.json for manual categorization

Usage: python3 scraper_gba.py
"""

import requests
from bs4 import BeautifulSoup
import json
import time
from urllib.parse import quote_plus
from datetime import datetime

def scrape_gba_sold_items(max_pages=10):
    """Scrape all Game Boy Advance sold items (last 3 months)"""

    print("="*70)
    print("🎮 SCRAPING GAME BOY ADVANCE SOLD ITEMS")
    print("="*70)
    print()

    # Search term
    search_term = "game boy advance"
    encoded_term = quote_plus(search_term)

    all_items = []
    seen_ids = set()

    # Headers (same as working GBC scraper)
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

    # Initialize session (CRITICAL: must look like a real browser)
    print("🌐 Initializing session with eBay.fr...")
    session = requests.Session()
    try:
        # Visit homepage first
        session.get('https://www.ebay.fr', headers=headers, timeout=10)
        time.sleep(3)  # Longer wait

        # Visit category page next (natural browsing)
        session.get('https://www.ebay.fr/b/Consoles-de-jeux-video/139971/bn_1865037', headers=headers, timeout=10)
        time.sleep(3)

        print("✅ Session initialized\n")
    except Exception as e:
        print(f"⚠️  Session init failed: {e}, continuing anyway...\n")

    # Scrape multiple pages
    for page_num in range(1, max_pages + 1):
        print(f"📄 Page {page_num}/{max_pages}")

        # Build URL
        # LH_Sold=1 = Sold listings
        # LH_Complete=1 = Completed listings
        # _sop=10 = Sort by price + shipping (lowest first)
        # _ipg=240 = Items per page (max)
        # Note: Removed _sacat to avoid eBay blocking (we filter by title instead)
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

            # Find items (sold listings use .s-card, NOT .s-item!)
            items = soup.select('.s-card.s-card--horizontal')
            print(f"  Found {len(items)} cards on page")

            page_items = 0

            for item in items:
                try:
                    # Get title (.s-card uses .su-styled-text.primary, NOT .s-item__title)
                    title_elem = item.select_one('.su-styled-text.primary')
                    if not title_elem:
                        continue

                    title = title_elem.get_text().strip()

                    # Skip "Shop on eBay" headers
                    if 'Shop on eBay' in title or title == '':
                        continue

                    # Only keep Game Boy Advance items
                    title_lower = title.lower()
                    if 'game boy advance' not in title_lower and 'gameboy advance' not in title_lower and 'gba' not in title_lower:
                        continue

                    # Get URL and item ID (.s-card uses .s-card__link)
                    link_elem = item.select_one('.s-card__link')
                    if not link_elem:
                        continue

                    url = link_elem.get('href', '')
                    # Extract item ID from URL
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

                    # Get price (sold price) - .s-card uses .s-card__price
                    price_elem = item.select_one('.s-card__price')
                    if not price_elem:
                        continue

                    price_text = price_elem.get_text().strip()

                    # Parse price
                    try:
                        # Remove "EUR" and convert to float
                        price_clean = price_text.replace('EUR', '').replace(',', '.').strip()
                        # Handle price ranges (take first price)
                        if 'à' in price_clean:
                            price_clean = price_clean.split('à')[0].strip()
                        # Remove any text after the number
                        price_clean = price_clean.split()[0] if price_clean else '0'
                        price = float(price_clean)
                    except:
                        continue

                    # Get sold date (.s-card structure)
                    date_elem = item.select_one('.su-styled-text.POSITIVE')
                    sold_date = ''
                    if date_elem:
                        date_text = date_elem.get_text().strip()
                        # Parse "Vendu le 20 déc. 2025" or "Sold 20 Dec 2025"
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
                                # Look for day (number)
                                if part.isdigit() and 1 <= int(part) <= 31 and day is None:
                                    day = part.zfill(2)
                                # Look for month (French/English abbreviation)
                                elif part in months_fr:
                                    month = months_fr[part]
                                # Look for year (4 digits)
                                elif part.isdigit() and len(part) == 4:
                                    year = part

                            if day and month and year:
                                sold_date = f"{year}-{month}-{day}"
                        except:
                            pass

                    # If no date found, use today
                    if not sold_date:
                        sold_date = datetime.now().strftime('%Y-%m-%d')

                    # Get condition (.s-card uses .su-styled-text.secondary)
                    condition = 'Occasion'  # Default
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
                        'url': url.split('?')[0]  # Clean URL
                    })

                    page_items += 1

                except Exception as e:
                    print(f"  ⚠️  Error parsing item: {e}")
                    continue

            print(f"  ✅ Extracted {page_items} valid GBA items")
            print(f"  📊 Total unique items so far: {len(all_items)}\n")

            # If we got less than 50 items on this page, probably reached the end
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
    print(f"Total unique GBA items found: {len(all_items)}")
    print()

    # Save raw data
    output_file = 'scraped_data_gba_raw.json'

    # Structure: just a flat list of all items (will be sorted manually)
    output_data = {
        'raw_items': all_items,
        'metadata': {
            'total_items': len(all_items),
            'scrape_date': datetime.now().isoformat(),
            'search_term': search_term,
            'console_type': 'game-boy-advance'
        }
    }

    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(output_data, f, indent=2, ensure_ascii=False)

    print(f"✅ Saved to: {output_file}")
    print()
    print("🎯 NEXT STEPS:")
    print("1. Run: python3 create_gba_variant_sorter.py")
    print("2. Open variant_sorter_gba.html in browser")
    print("3. Sort items into variants (keyboard shortcuts: K/B/P/R)")
    print("4. Export sorted JSON when done")
    print()

    return all_items

if __name__ == '__main__':
    scrape_gba_sold_items(max_pages=10)
