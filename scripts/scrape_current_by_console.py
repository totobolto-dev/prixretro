#!/usr/bin/env python3
"""
Scrape current eBay listings by console and save to JSON for manual sorting
"""
import os
import sys
import json
import time
from urllib.parse import quote_plus
from datetime import datetime
from playwright.sync_api import sync_playwright

def scrape_console_listings(console_name, category='139971', max_items=100):
    """Scrape current eBay listings for an entire console"""

    print(f"\n🔍 Scraping: {console_name}")

    search_term = console_name
    encoded_term = quote_plus(search_term)

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_sacat={category}&"
        f"_sop=10&"  # Sort by price lowest
        f"_ipg=100"  # 100 items per page
    )

    listings = []

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()

        try:
            print(f"  📡 Fetching: {url}")
            page.goto(url, wait_until='domcontentloaded', timeout=30000)
            page.wait_for_selector('.s-item', timeout=10000)

            cards = page.query_selector_all('.s-item')
            print(f"  📊 Found {len(cards)} items")

            for card in cards[:max_items]:
                try:
                    # Get title
                    title_elem = card.query_selector('.s-item__title')
                    if not title_elem:
                        continue
                    title = title_elem.inner_text().strip()

                    # Skip headers
                    if 'Shop on eBay' in title or not title:
                        continue

                    # Get URL
                    link_elem = card.query_selector('.s-item__link')
                    if not link_elem:
                        continue
                    item_url = link_elem.get_attribute('href').split('?')[0]

                    # Extract item ID
                    item_id = None
                    if '/itm/' in item_url:
                        parts = item_url.split('/itm/')
                        if len(parts) > 1:
                            item_id = parts[1].split('?')[0].split('/')[0]

                    if not item_id:
                        continue

                    # Get price
                    price_elem = card.query_selector('.s-item__price')
                    if not price_elem:
                        continue
                    price_text = price_elem.inner_text().strip()

                    # Parse price
                    try:
                        price_clean = price_text.replace('EUR', '').replace(',', '.').replace(' ', '').strip()
                        if 'à' in price_clean:
                            price_clean = price_clean.split('à')[0].strip()
                        price = float(price_clean.split()[0]) if price_clean else 0
                    except:
                        price = 0

                    if price <= 0:
                        continue

                    # Get image
                    img_elem = card.query_selector('.s-item__image-img')
                    image_url = img_elem.get_attribute('src') if img_elem else ''

                    listings.append({
                        'item_id': item_id,
                        'title': title,
                        'price': price,
                        'url': item_url,
                        'image_url': image_url,
                        'scraped_at': datetime.now().isoformat()
                    })

                    print(f"  ✅ {title[:60]}... - {price}€")

                except Exception as e:
                    print(f"  ⚠️  Error parsing item: {e}")
                    continue

            browser.close()

        except Exception as e:
            print(f"  ❌ Error: {e}")
            browser.close()

    return listings

def main():
    """Scrape all consoles"""
    print("🚀 Current Listings Scraper (By Console)")
    print("=" * 60)

    consoles = [
        {'name': 'Game Boy Color', 'slug': 'game-boy-color', 'category': '139971'},
        {'name': 'Game Boy Advance', 'slug': 'game-boy-advance', 'category': '139971'},
        {'name': 'Game Boy Advance SP', 'slug': 'game-boy-advance-sp', 'category': '139971'},
    ]

    results = {}

    for console in consoles:
        listings = scrape_console_listings(console['name'], console['category'], max_items=100)

        if listings:
            results[console['slug']] = {
                'console_name': console['name'],
                'total_listings': len(listings),
                'listings': listings,
                'scraped_at': datetime.now().isoformat()
            }
            print(f"  💾 Found {len(listings)} listings\n")
        else:
            print(f"  ℹ️  No listings found\n")

        # Be nice to eBay
        time.sleep(3)

    # Save to JSON
    output_file = f'current_listings_{datetime.now().strftime("%Y%m%d_%H%M%S")}.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(results, f, ensure_ascii=False, indent=2)

    print("=" * 60)
    print(f"✅ Scraping complete!")
    print(f"📁 Saved to: {output_file}")

    total = sum(len(data['listings']) for data in results.values())
    print(f"📊 Total listings: {total}")

if __name__ == '__main__':
    main()
