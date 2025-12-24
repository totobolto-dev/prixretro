#!/usr/bin/env python3
"""
Scrape current eBay listings (FOR SALE) with images using Playwright
Saves to current_listings.json
"""

import asyncio
import json
import time
from playwright.async_api import async_playwright

async def scrape_current_listings(page, variant_key, variant_name, max_items=5):
    """Scrape active eBay listings for a variant with images"""

    print(f"\n🔍 Scraping current listings for: {variant_name}")

    # Build search URL for items FOR SALE (not sold)
    # Use simple "game boy color" search for better results
    search_term = "game boy color"

    url = (
        f"https://www.ebay.fr/sch/i.html?"
        f"_nkw={search_term.replace(' ', '+')}&"
        f"_sacat=139971&"  # Video game consoles category
        f"_sop=10&"  # Sort by price + shipping (lowest first)
        f"_ipg=50"
        # NO LH_Sold - we want active listings
    )

    try:
        # Navigate to eBay search
        print(f"  📄 Loading: {url}")
        await page.goto(url, wait_until='domcontentloaded', timeout=60000)

        # Wait a bit for JavaScript to load
        await asyncio.sleep(2)

        # Wait for search results to load
        await page.wait_for_selector('.s-item', timeout=15000)

        # Extract all items
        items = await page.query_selector_all('.s-item')
        listings = []

        for item in items:
            try:
                # Skip sponsored items
                item_text = await item.inner_text()
                if 'SPONSORISÉ' in item_text or 'Shop on eBay' in item_text:
                    continue

                # Get title
                title_elem = await item.query_selector('.s-item__title')
                if not title_elem:
                    continue
                title = await title_elem.inner_text()
                title = title.strip()

                if not title or title == '':
                    continue

                # Only keep gameboy color items
                if 'game boy' not in title.lower() and 'gameboy' not in title.lower():
                    continue

                # Get URL
                link_elem = await item.query_selector('.s-item__link')
                if not link_elem:
                    continue
                item_url = await link_elem.get_attribute('href')
                item_url = item_url.split('?')[0] if item_url else ''

                # Get price
                price_elem = await item.query_selector('.s-item__price')
                if not price_elem:
                    continue
                price_text = await price_elem.inner_text()
                price_text = price_text.strip()

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
                condition_elem = await item.query_selector('.SECONDARY_INFO')
                condition = 'Occasion'
                if condition_elem:
                    condition = await condition_elem.inner_text()
                    condition = condition.strip()

                # Get image
                img_elem = await item.query_selector('.s-item__image-img')
                image_url = ''
                if img_elem:
                    image_url = await img_elem.get_attribute('src')
                    if not image_url:
                        image_url = ''

                listing = {
                    'title': title,
                    'url': item_url,
                    'price': price,
                    'condition': condition,
                    'image_url': image_url
                }

                listings.append(listing)
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


async def scrape_all_variants():
    """Scrape current listings for all variants using Playwright"""

    # Load scraped_data.json to get variant names
    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        scraped_data = json.load(f)

    all_current_listings = {}

    async with async_playwright() as p:
        # Launch browser in headless mode with stable arguments
        print("🚀 Launching browser...")
        browser = await p.chromium.launch(
            headless=True,
            args=[
                '--disable-blink-features=AutomationControlled',
                '--disable-dev-shm-usage',
                '--disable-gpu',
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--single-process',  # Prevent crashes in limited memory
            ]
        )

        for variant_key, variant_data in scraped_data.items():
            variant_name = variant_data['variant_name']

            # Create a FRESH page for each variant to prevent crashes
            page = await browser.new_page(
                viewport={'width': 1920, 'height': 1080},
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
            )

            try:
                listings = await scrape_current_listings(page, variant_key, variant_name, max_items=5)

                if listings:
                    all_current_listings[variant_key] = {
                        'variant_key': variant_key,
                        'variant_name': variant_name,
                        'listings': listings,
                        'count': len(listings)
                    }
                    print(f"  💤 Waiting 3 seconds before next variant...")
                    await asyncio.sleep(3)  # Be nice to eBay
                else:
                    print(f"  ⚠️  No listings found for {variant_name}")
            finally:
                # Always close the page to free memory
                await page.close()

        await browser.close()

    # Save to JSON
    output_file = 'current_listings.json'
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(all_current_listings, f, indent=2, ensure_ascii=False)

    print(f"\n✅ Saved current listings to: {output_file}")
    print(f"📊 Total variants with listings: {len(all_current_listings)}")

    return all_current_listings


if __name__ == '__main__':
    asyncio.run(scrape_all_variants())
