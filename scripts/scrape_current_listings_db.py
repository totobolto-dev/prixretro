#!/usr/bin/env python3
"""
Scrape current eBay listings and save directly to MySQL database
Queries variants from DB, scrapes active listings, inserts into current_listings table
"""

import os
import sys
import requests
from bs4 import BeautifulSoup
import time
from urllib.parse import quote_plus
from datetime import datetime
import mysql.connector
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

def get_db_connection():
    """Connect to MySQL database using environment variables"""
    return mysql.connector.connect(
        host=os.getenv('DB_HOST', 'ba2247864-001.eu.clouddb.ovh.net'),
        port=int(os.getenv('DB_PORT', '35831')),
        user=os.getenv('DB_USERNAME', 'prixretro_user'),
        password=os.getenv('DB_PASSWORD', 'f5bxVvfQUvkapKgNtjy5'),
        database=os.getenv('DB_DATABASE', 'prixretro'),
        charset='utf8mb4',
        collation='utf8mb4_unicode_ci'
    )

def get_variants():
    """Fetch all variants from database with console info"""
    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    query = """
        SELECT
            v.id,
            v.slug as variant_slug,
            v.name as variant_name,
            v.search_terms,
            c.id as console_id,
            c.slug as console_slug,
            c.name as console_name,
            c.search_term as console_search
        FROM variants v
        JOIN consoles c ON v.console_id = c.id
        WHERE c.is_active = 1
        ORDER BY c.id, v.id
    """

    cursor.execute(query)
    variants = cursor.fetchall()

    cursor.close()
    conn.close()

    return variants

def extract_item_id_from_url(url):
    """Extract eBay item ID from URL"""
    try:
        # URL format: https://www.ebay.fr/itm/123456789
        parts = url.split('/itm/')
        if len(parts) > 1:
            item_id = parts[1].split('?')[0].split('/')[0]
            return item_id
    except:
        pass
    return None

def scrape_variant_listings(variant, max_items=10):
    """Scrape current eBay listings for a specific variant"""

    variant_id = variant['id']
    variant_name = variant['variant_name']
    console_name = variant['console_name']
    console_slug = variant['console_slug']

    print(f"\n🔍 Scraping: {console_name} - {variant_name}")

    # Build search query - use console search term
    search_term = variant['console_search'] or console_name
    encoded_term = quote_plus(search_term)

    # Determine eBay category based on console
    if 'game-boy-color' in console_slug:
        category = '139971'  # Game Boy Color
    elif 'game-boy-advance' in console_slug:
        category = '139971'  # Same category for Game Boy Advance
    elif 'nintendo-ds' in console_slug or 'nintendo-3ds' in console_slug or 'nintendo-2ds' in console_slug:
        category = '139971'  # Same category for DS/3DS
    else:
        category = '139971'  # Default

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={encoded_term}&"
        f"_sacat={category}&"
        f"_sop=10&"  # Sort by price lowest first
        f"_ipg=100"  # Get more results
        # NO LH_Sold - we want active listings only
    )

    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept-Encoding': 'gzip, deflate, br',
        'DNT': '1',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Referer': 'https://www.ebay.fr/'
    }

    try:
        response = requests.get(url, headers=headers, timeout=15)
        response.raise_for_status()
        response.encoding = 'utf-8'

        soup = BeautifulSoup(response.text, 'html.parser')

        listings = []

        # Parse current listings using eBay's structure
        cards = soup.select('.s-item')
        print(f"  📊 Found {len(cards)} items")

        # Parse search terms from variant (JSON array in database)
        import json
        try:
            search_terms_list = json.loads(variant['search_terms']) if variant['search_terms'] else []
        except:
            search_terms_list = []

        for card in cards:
            try:
                # Get title
                title_elem = card.select_one('.s-item__title')
                if not title_elem:
                    continue
                title = title_elem.get_text().strip()

                # Skip shop headers
                if 'Shop on eBay' in title or title == '':
                    continue

                # Basic console name filter
                title_lower = title.lower()
                if console_name.lower() not in title_lower and 'game boy' not in title_lower and 'gameboy' not in title_lower:
                    continue

                # Match variant if search terms available
                if search_terms_list:
                    matched = False
                    for term in search_terms_list:
                        if term.lower() in title_lower:
                            matched = True
                            break
                    if not matched:
                        continue

                # Get URL
                link_elem = card.select_one('.s-item__link')
                if not link_elem:
                    continue
                item_url = link_elem.get('href', '').split('?')[0]

                # Extract item ID
                item_id = extract_item_id_from_url(item_url)
                if not item_id:
                    continue

                # Get price
                price_elem = card.select_one('.s-item__price')
                if not price_elem:
                    continue
                price_text = price_elem.get_text().strip()

                # Parse price
                try:
                    price_clean = price_text.replace('EUR', '').replace(',', '.').replace(' ', '').strip()
                    # Handle ranges - take first price
                    if 'à' in price_clean:
                        price_clean = price_clean.split('à')[0].strip()
                    price = float(price_clean.split()[0]) if price_clean else 0
                except:
                    continue

                if price <= 0:
                    continue

                listings.append({
                    'variant_id': variant_id,
                    'item_id': item_id,
                    'title': title[:255],  # Truncate to DB limit
                    'price': price,
                    'url': item_url[:500]  # Truncate to DB limit
                })

                print(f"  ✅ {title[:50]}... - {price}€")

                if len(listings) >= max_items:
                    break

            except Exception as e:
                print(f"  ⚠️  Error parsing item: {e}")
                continue

        return listings

    except Exception as e:
        print(f"  ❌ Error scraping: {e}")
        return []

def clear_old_listings():
    """Mark all current listings as sold before scraping new ones"""
    conn = get_db_connection()
    cursor = conn.cursor()

    cursor.execute("UPDATE current_listings SET is_sold = 1, updated_at = NOW()")
    affected = cursor.rowcount
    conn.commit()

    cursor.close()
    conn.close()

    print(f"🗑️  Marked {affected} old listings as sold")

def save_listings_to_db(listings):
    """Save listings to database (insert or update)"""
    if not listings:
        return 0

    conn = get_db_connection()
    cursor = conn.cursor()

    # Insert or update listings
    query = """
        INSERT INTO current_listings
            (variant_id, item_id, title, price, url, is_sold, last_seen_at, created_at, updated_at)
        VALUES
            (%s, %s, %s, %s, %s, 0, NOW(), NOW(), NOW())
        ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            price = VALUES(price),
            url = VALUES(url),
            is_sold = 0,
            last_seen_at = NOW(),
            updated_at = NOW()
    """

    inserted = 0
    for listing in listings:
        try:
            cursor.execute(query, (
                listing['variant_id'],
                listing['item_id'],
                listing['title'],
                listing['price'],
                listing['url']
            ))
            inserted += cursor.rowcount
        except Exception as e:
            print(f"  ⚠️  Error saving listing: {e}")
            continue

    conn.commit()
    cursor.close()
    conn.close()

    return inserted

def main():
    """Main scraping function"""
    print("🚀 Current Listings Scraper for PrixRetro")
    print("=" * 60)

    # Clear old listings
    clear_old_listings()

    # Get all variants
    print("\n📋 Fetching variants from database...")
    variants = get_variants()
    print(f"✅ Found {len(variants)} variants\n")

    total_listings = 0
    total_saved = 0

    # Scrape each variant
    for variant in variants:
        listings = scrape_variant_listings(variant, max_items=10)

        if listings:
            saved = save_listings_to_db(listings)
            total_listings += len(listings)
            total_saved += saved
            print(f"  💾 Saved {saved} listings")
        else:
            print(f"  ℹ️  No listings found")

        # Be nice to eBay
        time.sleep(3)

    print("\n" + "=" * 60)
    print(f"✅ Scraping complete!")
    print(f"📊 Total listings found: {total_listings}")
    print(f"💾 Total saved to database: {total_saved}")

if __name__ == '__main__':
    main()
