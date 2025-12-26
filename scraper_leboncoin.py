#!/usr/bin/env python3
"""
Leboncoin Scraper for Retrogaming Consoles
Scrapes active listings (not sold - Leboncoin doesn't show sold history)

Strategy:
- Scrape daily to track listings
- When listing disappears → assume sold
- Track price changes (seller drops price)
- Calculate "time to sell" metric

Usage: python3 scraper_leboncoin.py
"""

import requests
from bs4 import BeautifulSoup
import json
import time
from urllib.parse import quote_plus
from datetime import datetime
import os

def scrape_leboncoin_console(search_term, category="jeux_video", max_pages=3):
    """
    Scrape Leboncoin for console listings

    NOTE: Leboncoin has strong anti-bot measures:
    - May require JavaScript rendering (Playwright/Selenium)
    - May require proxy rotation
    - May require captcha solving

    This is a PROTOTYPE - test first before scaling
    """

    print("="*70)
    print(f"🔍 SCRAPING LEBONCOIN: {search_term}")
    print("="*70)
    print()
    print("⚠️  IMPORTANT: This is a prototype scraper")
    print("   Leboncoin has anti-bot measures - may need Playwright/proxies")
    print()

    # Leboncoin URL structure
    # Example: https://www.leboncoin.fr/recherche?category=74&text=game+boy+color
    # Category 74 = Consoles & jeux vidéo

    base_url = "https://www.leboncoin.fr/recherche"
    encoded_term = quote_plus(search_term)

    all_listings = []
    seen_ids = set()

    # Headers to look more like a browser
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept-Encoding': 'gzip, deflate, br',
        'DNT': '1',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Cache-Control': 'max-age=0',
        'Referer': 'https://www.leboncoin.fr/'
    }

    session = requests.Session()

    for page in range(1, max_pages + 1):
        print(f"📄 Page {page}/{max_pages}")

        # Build URL
        url = f"{base_url}?category=74&text={encoded_term}&page={page}"

        try:
            response = session.get(url, headers=headers, timeout=15)

            print(f"  Status: {response.status_code}")

            if response.status_code == 403:
                print("  ❌ BLOCKED: Leboncoin detected scraping")
                print("  💡 Solution: Use Playwright with headless browser")
                break

            if response.status_code == 429:
                print("  ⚠️  RATE LIMITED: Too many requests")
                print("  Waiting 60 seconds...")
                time.sleep(60)
                continue

            response.raise_for_status()

            # Parse HTML
            soup = BeautifulSoup(response.text, 'html.parser')

            # DEBUG: Save HTML to check structure
            if page == 1:
                with open('leboncoin_debug.html', 'w', encoding='utf-8') as f:
                    f.write(response.text)
                print("  💾 Saved HTML to leboncoin_debug.html for inspection")

            # TRY MULTIPLE SELECTORS (Leboncoin changes frequently)

            # Attempt 1: Look for listing cards
            cards = soup.select('[data-qa-id="aditem_container"]')
            if not cards:
                cards = soup.select('.styles_adCard__2YFTi')  # Old class
            if not cards:
                cards = soup.select('a[href*="/consoles_jeux_video/"]')  # Fallback: links

            print(f"  Found {len(cards)} potential listings")

            if len(cards) == 0:
                print("  ⚠️  No listings found - HTML structure may have changed")
                print("  📖 Check leboncoin_debug.html to update selectors")
                break

            page_count = 0

            for card in cards:
                try:
                    # Extract listing ID from URL
                    link = card.get('href', '')
                    if not link or '/consoles_jeux_video/' not in link:
                        continue

                    # Leboncoin ID is usually in URL: /consoles_jeux_video/1234567890.htm
                    try:
                        listing_id = link.split('/')[-1].split('.')[0]
                    except:
                        continue

                    if not listing_id or listing_id in seen_ids:
                        continue

                    seen_ids.add(listing_id)

                    # Extract title
                    title_elem = card.select_one('[data-qa-id="aditem_title"]')
                    if not title_elem:
                        title_elem = card.select_one('p[itemprop="name"]')
                    if not title_elem:
                        continue

                    title = title_elem.get_text().strip()

                    # Extract price
                    price_elem = card.select_one('[data-qa-id="aditem_price"]')
                    if not price_elem:
                        price_elem = card.select_one('span[itemprop="price"]')
                    if not price_elem:
                        continue

                    price_text = price_elem.get_text().strip()

                    # Parse price (format: "50 €" or "50€")
                    try:
                        price = float(price_text.replace('€', '').replace(' ', '').replace(',', '.').strip())
                    except:
                        continue

                    # Extract location (optional)
                    location_elem = card.select_one('[data-qa-id="aditem_location"]')
                    location = location_elem.get_text().strip() if location_elem else "France"

                    # Build full URL
                    full_url = f"https://www.leboncoin.fr{link}" if link.startswith('/') else link

                    # Add listing
                    listing = {
                        'listing_id': listing_id,
                        'title': title,
                        'price': price,
                        'location': location,
                        'url': full_url,
                        'scraped_date': datetime.now().isoformat(),
                        'marketplace': 'leboncoin',
                        'status': 'active'  # Will track if it disappears
                    }

                    all_listings.append(listing)
                    page_count += 1

                except Exception as e:
                    print(f"  ⚠️  Error parsing listing: {e}")
                    continue

            print(f"  ✅ Extracted {page_count} listings")
            print(f"  📊 Total: {len(all_listings)} unique listings\n")

            # Be very nice to Leboncoin (avoid ban)
            time.sleep(5)

        except Exception as e:
            print(f"  ❌ Error on page {page}: {e}\n")
            break

    print("="*70)
    print("📊 SCRAPING COMPLETE")
    print("="*70)
    print(f"Total listings found: {len(all_listings)}")
    print()

    if len(all_listings) == 0:
        print("⚠️  NO LISTINGS FOUND")
        print()
        print("Possible reasons:")
        print("1. Leboncoin blocked the scraper (403/429)")
        print("2. HTML structure changed (selectors outdated)")
        print("3. No active listings for this search")
        print()
        print("💡 Next steps:")
        print("1. Check leboncoin_debug.html")
        print("2. Update selectors based on actual HTML")
        print("3. Consider using Playwright (JavaScript rendering)")
        print("4. Use proxies if getting blocked")
        return []

    # Save to JSON
    output_file = f"leboncoin_{search_term.replace(' ', '_')}.json"

    # Load existing data if present (for tracking)
    existing_data = {}
    if os.path.exists(output_file):
        try:
            with open(output_file, 'r', encoding='utf-8') as f:
                existing_data = json.load(f)
        except:
            pass

    # Prepare output
    output = {
        'listings': all_listings,
        'metadata': {
            'search_term': search_term,
            'scrape_date': datetime.now().isoformat(),
            'total_found': len(all_listings),
            'marketplace': 'leboncoin'
        },
        'history': existing_data.get('history', [])
    }

    # Add this scrape to history
    output['history'].append({
        'date': datetime.now().isoformat(),
        'count': len(all_listings),
        'listing_ids': [l['listing_id'] for l in all_listings]
    })

    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(output, f, indent=2, ensure_ascii=False)

    print(f"✅ Saved to: {output_file}")
    print()
    print("💡 NEXT STEPS:")
    print("1. Run this daily to track listings")
    print("2. When listing disappears → assume sold")
    print("3. Calculate 'time to sell' metric")
    print("4. Integrate with main data pipeline")
    print()

    return all_listings

def test_leboncoin_scraper():
    """Test scraper with Game Boy Color"""
    print("🧪 TESTING LEBONCOIN SCRAPER")
    print()
    print("This is a PROTOTYPE - testing with 3 pages max")
    print("If it works, we can scale to more searches + daily automation")
    print()

    # Test with GBC
    listings = scrape_leboncoin_console("game boy color", max_pages=3)

    if len(listings) > 0:
        print("✅ SUCCESS! Scraper works!")
        print()
        print(f"Sample listing:")
        print(f"  Title: {listings[0]['title']}")
        print(f"  Price: {listings[0]['price']}€")
        print(f"  Location: {listings[0]['location']}")
        print(f"  URL: {listings[0]['url']}")
    else:
        print("❌ FAILED - See errors above")
        print()
        print("Common fixes:")
        print("1. Update selectors in scraper_leboncoin.py")
        print("2. Switch to Playwright (JavaScript rendering)")
        print("3. Use rotating proxies")

if __name__ == '__main__':
    test_leboncoin_scraper()
