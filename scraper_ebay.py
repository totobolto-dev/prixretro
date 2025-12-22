#!/usr/bin/env python3
"""
PrixRetro - Real eBay.fr Scraper
Scrapes actual sold listings from eBay.fr for Game Boy Color variants
"""

import requests
from bs4 import BeautifulSoup
import json
import time
import random
import os
from datetime import datetime
from urllib.parse import quote_plus
import re

class EbayScraper:
    def __init__(self, config_path='config.json'):
        """Initialize scraper with configuration"""
        with open(config_path, 'r', encoding='utf-8') as f:
            self.config = json.load(f)
        
        # Load existing scraped data to track seen IDs
        self.existing_data = {}
        if os.path.exists('scraped_data.json'):
            try:
                with open('scraped_data.json', 'r', encoding='utf-8') as f:
                    self.existing_data = json.load(f)
                print("📂 Loaded existing data for incremental scraping")
            except:
                print("⚠️  Could not load existing data, starting fresh")
        
        # Ultra-realistic headers to bypass bot detection
        self.headers = {
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
        }
        
        self.session = requests.Session()
        self.session.headers.update(self.headers)
        
        # Initialize session by visiting homepage first (like a real user)
        try:
            print("🌐 Initializing session with eBay.fr...")
            self.session.get('https://www.ebay.fr', timeout=10)
            time.sleep(2)  # Pause like a real user
            print("✅ Session initialized")
        except:
            print("⚠️  Session init failed, continuing anyway...")
    
    def search_sold_listings(self, search_term, max_results=None, full_history=False, date_from=None, date_to=None):
        """
        Search for sold listings on eBay.fr with optional date range
        
        Args:
            search_term: Search query (e.g., "game boy color violet")
            max_results: Maximum number of results per page (default from config)
            full_history: If True, paginate through ALL pages (can be 1000+ items!)
            date_from: Start date for filtering (datetime object)
            date_to: End date for filtering (datetime object)
        
        Returns:
            List of dictionaries with listing data
        """
        if max_results is None:
            max_results = self.config.get('scraping', {}).get('max_results_per_search', 60)
        
        all_listings = []
        page = 1
        # SCRAPING STRATEGY:
        # - full_history=True: Scrape 2 pages (all available Game Boy Color results)
        # - Otherwise: Just page 1
        max_pages = 2 if full_history else 1
        items_per_page = max_results if max_results else 240  # eBay allows up to 240 per page

        # Track seen item IDs to prevent duplicates during pagination
        seen_item_ids = set()

        while page <= max_pages:
            retry_same_page = False  # Flag for CAPTCHA retry
            print(f"🔍 Searching: '{search_term}' (page {page}/{max_pages if full_history else 1})")

            # eBay.fr sold items URL with PAGE NUMBER PAGINATION and optional DATE RANGE
            # _ipg: Items per page (60 is standard, eBay may ignore values > 60)
            # _pgn: Page number (1, 2, 3, etc.) - PROPER pagination!
            # LH_SoldFrom/LH_SoldTo: Date range filtering (MM/DD/YYYY format)
            encoded_term = quote_plus(search_term)
            url = (
                f"https://www.ebay.fr/sch/i.html?"
                f"_nkw={encoded_term}&"
                f"_sacat=139971&"
                f"_from=R40&"
                f"_fsrp=1&"
                f"_sop=10&"
                f"_ipg=240&"
                f"LH_Sold=1&"
                f"_pgn={page}"
            )
            
            # Add date range parameters if specified
            if date_from and date_to:
                date_from_str = date_from.strftime('%m/%d/%Y')
                date_to_str = date_to.strftime('%m/%d/%Y')
                url += f"&LH_SoldFrom={date_from_str}&LH_SoldTo={date_to_str}"
                print(f"   📅 Date range: {date_from_str} to {date_to_str}")
            
            # Add referer to look more legit
            request_headers = self.headers.copy()
            request_headers['Referer'] = 'https://www.ebay.fr/'
            
            try:
                response = self.session.get(url, headers=request_headers, timeout=15)
                response.raise_for_status()
                
                # DEBUG: Check what we got
                print(f"   DEBUG: Response URL: {response.url[:100]}")
                print(f"   DEBUG: Response length: {len(response.text)} chars")
                has_challenge = 'challenge' in response.url.lower()
                has_captcha = 'captcha' in response.text.lower()
                print(f"   DEBUG: has_challenge={has_challenge}, has_captcha={has_captcha}")
                
                # Check if we got blocked
                if has_challenge or has_captcha:
                    print(f"⚠️  Challenge/CAPTCHA detected - eBay is blocking!")
                    print(f"")
                    print(f"🛑 PAUSE: Change ton IP Mullvad, puis appuie sur Entrée pour continuer...")
                    print(f"   (Le scraper va retry la page {page})")
                    user_input = input(">>> Appuie sur Entrée: ")
                    print(f"   Received input: '{user_input}'")
                    
                    # Reset session to clear cookies/fingerprint
                    print(f"   🔄 Resetting session (clearing cookies)...")
                    self.session = requests.Session()
                    self.session.headers.update(self.headers)
                    
                    retry_same_page = True
                    # Wait longer after IP change
                    print(f"   ⏳ Waiting 30 seconds for IP to propagate...")
                    time.sleep(30)
                    print(f"   Retrying page {page}...")
                    continue
                
                if len(response.text) < 10000:
                    print(f"⚠️  Response too short ({len(response.text)} chars) - possible block")
                    if page == 1:
                        return []
                    break
                
                soup = BeautifulSoup(response.text, 'html.parser')
                
                listings = []
                
                # eBay uses different selectors depending on layout
                # Try multiple selector patterns, excluding carousel items
                item_selectors = [
                    'li.s-item:not([class*="carousel"])',  # Standard items, not carousel
                    'div.s-item:not([class*="carousel"])',  # Alternative layout
                    'ul.srp-results li.s-item',  # Items in results list
                    'div[class*="srp-river-results"] ul li.s-item',  # Nested results
                ]
                
                items = []
                for selector in item_selectors:
                    items = soup.select(selector)
                    if items:
                        print(f"   Using selector: {selector}")
                        break
                
                # Fallback: get all li in results, filter manually
                if not items:
                    print(f"   Trying fallback: manual filtering...")
                    all_lis = soup.select('div[class*="srp-river-results"] li, .srp-results li')
                    # Filter out carousel, ads, and other non-product items
                    items = [
                        li for li in all_lis 
                        if 'carousel' not in ' '.join(li.get('class', [])).lower()
                        and 'srp-river-answer' not in ' '.join(li.get('class', [])).lower()
                    ]
                    print(f"   Filtered to {len(items)} potential items")
                
                if not items:
                    print(f"⚠️  No items found on page {page}")
                    if page == 1:
                        print(f"📄 Response length: {len(response.text)} chars")
                        # Save HTML for debugging
                        with open(f'debug_ebay_{search_term[:20]}_p{page}.html', 'w', encoding='utf-8') as f:
                            f.write(response.text)
                        print(f"💾 Saved HTML for inspection")
                        return []
                    break  # No more pages
                
                print(f"📦 Found {len(items)} items on page {page}")
                
                for item in items[:max_results]:
                    try:
                        listing = self._parse_listing(item)
                        if listing and listing['price'] > 0:
                            # Check for duplicates within this pagination session
                            item_id = listing.get('item_id')
                            if item_id and item_id not in seen_item_ids:
                                listings.append(listing)
                                seen_item_ids.add(item_id)
                            elif item_id in seen_item_ids:
                                print(f"      ⏭️  SKIP (duplicate): {listing['title'][:60]}")
                        elif listing is None:
                            # Item was filtered out
                            pass
                        else:
                            # Debug: item parsed but price was 0
                            pass
                    except Exception as e:
                        # Skip items that fail to parse
                        print(f"      ⚠️  Parse error: {str(e)[:50]}")
                        continue

                print(f"✅ Parsed {len(listings)} valid listings from page {page} ({len(seen_item_ids)} unique total)")

                # Add to all_listings
                all_listings.extend(listings)
                
                # Check if we should continue pagination
                if not full_history:
                    break  # Single page mode
                
                # Check raw items count, not parsed count (filters may reject many)
                if len(items) < 50:  # eBay typically has 60+ items per page
                    print(f"📄 Page {page} had only {len(items)} raw items, probably last page")
                    break
                
                # Rate limiting between pages
                if page < max_pages:
                    wait_time = random.uniform(8, 15)  # Longer wait to avoid CAPTCHA
                    print(f"⏸️  Waiting {wait_time:.1f}s before next page...")
                    time.sleep(wait_time)

                # Only increment if not retrying same page
                if not retry_same_page:
                    page += 1
                
            except requests.RequestException as e:
                print(f"❌ Error fetching page {page}: {e}")
                if page == 1:
                    return []
                break  # Return what we have
        
        print(f"🎯 Total: {len(all_listings)} listings across {page} page(s)")
        return all_listings
    
    def _parse_listing(self, item):
        """Parse a single listing item - Updated for eBay's current structure"""
        
        # Skip carousel items, ads, and other non-products
        item_classes = ' '.join(item.get('class', [])).lower()
        skip_keywords = ['carousel', 'srp-river-answer']
        if any(keyword in item_classes for keyword in skip_keywords):
            return None
        
        # Title - eBay uses su-styled-text primary in s-card__title
        title = None
        title_selectors = [
            '.s-card__title span.su-styled-text.primary',
            '.s-card__title span',
            'div[role="heading"] span',
            'span.su-styled-text.primary.default',
        ]
        
        for selector in title_selectors:
            title_elem = item.select_one(selector)
            if title_elem:
                title = title_elem.get_text(strip=True)
                if title and len(title) > 10:  # Valid title
                    break
        
        if not title:
            return None
        
        # Skip only obvious ads
        if len(title) < 10 or 'Shop on eBay' in title:
            print(f"      ❌ REJECT (ad/short): {title[:50]}")
            return None
        
        # SMART FILTERS - Use precise phrases to avoid false positives
        title_lower = title.lower()
        
        # Must mention game boy or gameboy
        if 'game boy' not in title_lower and 'gameboy' not in title_lower:
            print(f"      ⏭️  SKIP (not gameboy): {title[:60]}")
            return None

        print(f"      ✅ ACCEPT: {title[:80]}")
        
        # Price - Look for s-card__price or su-styled-text with EUR
        price = 0
        price_selectors = [
            'span.s-card__price',
            'span.su-styled-text.positive.bold',
            'span[class*="price"]',
        ]
        
        for selector in price_selectors:
            price_elem = item.select_one(selector)
            if price_elem:
                price_text = price_elem.get_text(strip=True)
                price = self._parse_price(price_text)
                if price > 0:
                    break
        
        # Fallback: search for any span with EUR
        if price <= 0:
            for span in item.find_all('span'):
                text = span.get_text(strip=True)
                if 'EUR' in text or '€' in text:
                    price = self._parse_price(text)
                    if price > 0:
                        break
        
        if price <= 0:
            print(f"      ❌ NO PRICE FOUND: {title[:60]}")
            return None
        
        # URL - s-card__link
        url = ""
        link_selectors = [
            'a.s-card__link',
            'a[href*="/itm/"]',
        ]
        
        for selector in link_selectors:
            link_elem = item.select_one(selector)
            if link_elem:
                url = link_elem.get('href', '')
                if url and '/itm/' in url:
                    # Clean URL
                    if '?' in url:
                        base_url = url.split('?')[0]
                        # Keep just the item ID
                        if '/itm/' in base_url:
                            url = base_url
                    break
        
        # Extract item ID from URL
        item_id = None
        if url and '/itm/' in url:
            id_match = re.search(r'/itm/(\d+)', url)
            if id_match:
                item_id = id_match.group(1)
        
        if not item_id:
            print(f"      ⚠️  No item ID: {title[:60]}")
            return None
        
        # Date sold - Look for "Vendu le" text
        # IMPORTANT: Only keep items that were actually SOLD
        date_sold = None
        has_vendu = False

        # Try multiple approaches to find sold date
        # 1. Look in all text elements
        for elem in item.find_all(['span', 'div', 'time']):
            text = elem.get_text(strip=True)
            # More comprehensive sold indicators
            if any(indicator in text.lower() for indicator in [
                'vendu le', 'vendu ', 'sold on', 'sold ', 'terminé le', 
                'ended ', 'se termine le', 'fin le'
            ]):
                has_vendu = True
                # Try to extract the date part - support multiple formats

                # Comprehensive French month dictionary (all variations)
                months_fr = {
                    # Full names
                    'janvier': '01', 'février': '02', 'fevrier': '02', 'mars': '03',
                    'avril': '04', 'mai': '05', 'juin': '06', 'juillet': '07',
                    'août': '08', 'aout': '08', 'septembre': '09', 'octobre': '10',
                    'novembre': '11', 'décembre': '12', 'decembre': '12',
                    # Common abbreviations with accents
                    'janv': '01', 'févr': '02', 'mars': '03', 'avr': '04',
                    'mai': '05', 'juin': '06', 'juil': '07', 'août': '08',
                    'sept': '09', 'oct': '10', 'nov': '11', 'déc': '12',
                    # Without accents
                    'janv': '01', 'fevr': '02', 'avr': '04', 'juil': '07',
                    'aout': '08', 'sept': '09', 'dec': '12',
                    # Encoding-mangled versions (eBay encoding issues)
                    'd�c': '12', 'f�vr': '02', 'ao�t': '08',
                    # Short forms
                    'jan': '01', 'fev': '02', 'mar': '03', 'avr': '04',
                    'jun': '06', 'jul': '07', 'aug': '08', 'sep': '09',
                    'oct': '10', 'nov': '11', 'dec': '12',
                    # English (in case eBay uses English)
                    'january': '01', 'february': '02', 'march': '03', 'april': '04',
                    'may': '05', 'june': '06', 'july': '07', 'august': '08',
                    'september': '09', 'october': '10', 'november': '11', 'december': '12',
                }

                # Try format: "jeu. 18 déc., 22:04" or "18 déc. 2025"
                # Pattern handles: "jeu. 18 déc., 22:04", "Vendu le  18 d�c. 2025", etc.
                # First try with day-of-week and time: "jeu. 18 déc., 22:04"
                date_match = re.search(r'(?:[a-z]+\.?\s+)?(\d{1,2})\s+([a-zéèêû�]+)\.?,?\s+(?:(\d{4})|(\d{1,2}:\d{2}))', text, re.IGNORECASE)
                if date_match:
                    day, month_str, year, time = date_match.groups()
                    # If no year found but time is present, use current year
                    # But if the date is in the future, use previous year
                    if not year and time:
                        current_date = datetime.now()
                        year = str(current_date.year)
                        # If the parsed date would be in the future, use previous year
                        month_num_int = int(months_fr.get(month_str.lower(), '01'))
                        day_int = int(day)
                        try:
                            test_date = datetime(int(year), month_num_int, day_int)
                            if test_date > current_date:
                                year = str(current_date.year - 1)
                        except ValueError:
                            pass  # Keep current year if date is invalid
                    month_num = months_fr.get(month_str.lower())
                    if month_num and year:
                        date_sold = f"{year}-{month_num}-{day.zfill(2)}"
                
                # Fallback: Try format: "18 déc. 2025" - handle extra spaces and encoding issues
                if not date_sold:
                    date_match = re.search(r'(\d{1,2})\s+([a-zéèêû�]+)\.?\s+(\d{4})', text, re.IGNORECASE)
                    if date_match:
                        day, month_str, year = date_match.groups()
                        month_num = months_fr.get(month_str.lower())
                        if month_num:
                            date_sold = f"{year}-{month_num}-{day.zfill(2)}"

                # Try format: "18/12/2025" or "18-12-2025"
                if not date_sold:
                    date_match = re.search(r'(\d{1,2})[/-](\d{1,2})[/-](\d{4})', text)
                    if date_match:
                        day, month, year = date_match.groups()
                        date_sold = f"{year}-{month.zfill(2)}-{day.zfill(2)}"

                # Try format: "Dec 18, 2025" (English)
                if not date_sold:
                    date_match = re.search(r'([a-z]+)\s+(\d{1,2}),?\s+(\d{4})', text, re.IGNORECASE)
                    if date_match:
                        month_str, day, year = date_match.groups()
                        month_num = months_fr.get(month_str.lower())
                        if month_num:
                            date_sold = f"{year}-{month_num}-{day.zfill(2)}"

                if date_sold:
                    break
        
        # If no sold date found, use default from _extract_sold_date (today's date)
        if not date_sold:
            date_sold = datetime.now().strftime("%Y-%m-%d")
            print(f"      ⚠️  No sold date found, using today: {date_sold}")
        
        # Condition - Look for "Occasion", "Neuf", etc
        condition = self._extract_condition(item)
        
        return {
            'item_id': item_id,
            'title': title[:150],
            'price': price,
            'url': url,
            'sold_date': date_sold,
            'condition': condition
        }
    
    def _parse_price(self, price_text):
        """Extract numeric price from text"""
        # Remove currency symbols and spaces
        # eBay.fr uses format like "59,99 EUR" or "59,99 €"
        price_text = price_text.replace('EUR', '').replace('€', '').strip()
        
        # Handle price ranges (e.g., "50,00 à 100,00")
        if 'à' in price_text:
            # Take the first price
            price_text = price_text.split('à')[0].strip()
        
        # Replace comma with dot for float conversion
        price_text = price_text.replace(',', '.').replace(' ', '')
        
        try:
            # Extract first number found
            match = re.search(r'(\d+\.?\d*)', price_text)
            if match:
                return float(match.group(1))
        except (ValueError, AttributeError):
            pass
        
        return 0.0
    
    def _extract_sold_date(self, item):
        """Try to extract sold date from listing"""
        # eBay shows dates like "Vendu le 16 déc. 2025"
        
        # Look for "Vendu le" text
        for elem in item.find_all(['span', 'div']):
            text = elem.get_text(strip=True)
            if 'Vendu le' in text or 'Vendu' in text:
                # Try to extract the date part
                # Format: "Vendu le 16 déc. 2025"
                date_match = re.search(r'(\d{1,2})\s+(\w+)\.?\s+(\d{4})', text)
                if date_match:
                    day, month_fr, year = date_match.groups()
                    # Convert French month to number
                    months_fr = {
                        'janv': '01', 'févr': '02', 'mars': '03', 'avr': '04',
                        'mai': '05', 'juin': '06', 'juil': '07', 'août': '08',
                        'sept': '09', 'oct': '10', 'nov': '11', 'déc': '12'
                    }
                    month_num = months_fr.get(month_fr.lower(), '01')
                    # Return in YYYY-MM-DD format for easy sorting and monthly grouping
                    return f"{year}-{month_num}-{day.zfill(2)}"
        
        # Default to today in YYYY-MM-DD format
        return datetime.now().strftime("%Y-%m-%d")
    
    def _extract_condition(self, item):
        """Extract condition from listing"""
        # Look for condition text - eBay uses "Occasion", "Neuf", etc.
        
        # Check su-styled-text secondary for condition
        for elem in item.select('span.su-styled-text.secondary'):
            text = elem.get_text(strip=True).lower()
            
            if 'neuf' in text:
                return "Neuf"
            elif 'très bon' in text:
                return "Très bon état"
            elif 'bon' in text:
                return "Bon état"
            elif 'occasion' in text:
                return "Occasion"
            elif 'reconditionné' in text:
                return "Reconditionné"
            elif 'acceptable' in text:
                return "État acceptable"
        
        # Default
        return "Occasion"
    
    def scrape_recent_complete(self, search_term="game boy color"):
        """
        Scrape all sold listings from eBay's 90-day limit (approximately 285 total results)
        Since eBay only shows 90 days of history, we'll get everything in one comprehensive search
        
        Args:
            search_term: Search term to use (default: "game boy color")
        
        Returns:
            List of all found listings from the past 90 days
        """
        from datetime import datetime, timedelta
        
        print(f"\n{'='*60}")
        print(f"📊 Complete Recent Scraping: '{search_term}'")
        print(f"📅 eBay's 90-day sold listings limit (~285 total results)")
        print(f"{'='*60}")
        
        print(f"\n🔍 Searching all available sold listings...")
        
        try:
            # Get ALL available sold listings (eBay's 90-day limit)
            # Use full_history=True to paginate through all available pages
            all_listings = self.search_sold_listings(
                search_term=search_term,
                max_results=240,  # Max items per page
                full_history=True  # Get all pages
            )
            
            print(f"✅ Found {len(all_listings)} total listings")
            
            # Remove duplicates by item_id
            seen_ids = set()
            unique_listings = []
            for listing in all_listings:
                item_id = listing.get('item_id')
                if item_id and item_id not in seen_ids:
                    seen_ids.add(item_id)
                    unique_listings.append(listing)
            
            if len(unique_listings) != len(all_listings):
                print(f"🔄 Removed {len(all_listings) - len(unique_listings)} duplicates")
            
            print(f"\n🎯 Complete scraping finished!")
            print(f"   📊 Unique listings: {len(unique_listings)}")
            print(f"   📈 Expected ~285 based on eBay search")
            
            return unique_listings
            
        except Exception as e:
            print(f"❌ Error during complete scraping: {e}")
            return []
    
    def scrape_variant(self, variant_key, variant_data):
        """
        Scrape all listings for a specific variant
        
        Args:
            variant_key: Variant identifier (e.g., 'violet')
            variant_data: Variant configuration with search terms
        
        Returns:
            Dictionary with variant data and listings
        """
        print(f"\n{'='*60}")
        print(f"🎮 Scraping: {variant_data['name']}")
        print(f"{'='*60}")
        
        all_listings = []
        
        # Get existing seen IDs for this variant
        seen_ids = set()
        is_first_run = variant_key not in self.existing_data or not self.existing_data[variant_key].get('listings')
        
        if not is_first_run:
            existing_listings = self.existing_data[variant_key].get('listings', [])
            seen_ids = {l.get('item_id') for l in existing_listings if l.get('item_id')}
            print(f"📂 Found {len(seen_ids)} existing IDs (incremental mode)")
        else:
            print(f"🆕 First run - FULL HISTORY mode (will paginate through all pages!)")
        
        # Try each search term for this variant
        max_results = self.config.get('scraping', {}).get('max_results_per_search', 60)
        for search_term in variant_data['search_terms']:
            # Use full_history=True on first run, False on incremental updates
            listings = self.search_sold_listings(
                search_term, 
                max_results=max_results,
                full_history=is_first_run
            )
            
            # Filter out already-seen IDs
            new_listings = [l for l in listings if l.get('item_id') not in seen_ids]
            if len(new_listings) < len(listings):
                print(f"   ⏭️  Skipped {len(listings) - len(new_listings)} already-seen items")
            
            all_listings.extend(new_listings)
            
            # Rate limiting - be nice to eBay (longer pauses to avoid detection)
            time.sleep(random.uniform(3, 5))  # Random 3-5 seconds
        
        # Merge with existing listings
        if variant_key in self.existing_data:
            existing_listings = self.existing_data[variant_key].get('listings', [])
            all_listings.extend(existing_listings)
            print(f"   📚 Merged {len(existing_listings)} existing listings")
        
        # Remove duplicates based on item_id
        seen_ids = set()
        unique_listings = []
        for listing in all_listings:
            item_id = listing.get('item_id')
            if item_id and item_id not in seen_ids:
                seen_ids.add(item_id)
                unique_listings.append(listing)
        
        # Sort by date (most recent first)
        unique_listings.sort(key=lambda x: x['sold_date'], reverse=True)
        
        # Keep ALL listings for full history
        final_listings = unique_listings
        
        print(f"📊 Final count: {len(final_listings)} unique listings")
        
        # Calculate statistics WITH outlier filtering
        if final_listings:
            prices = [l['price'] for l in final_listings]
            
            # Remove outliers (prices that are ±30% from median)
            outlier_threshold = self.config.get('scraping', {}).get('outlier_threshold_percent', 30)
            
            if len(prices) >= 5:  # Only filter outliers if we have enough data
                median_price = sorted(prices)[len(prices) // 2]
                lower_bound = median_price * (1 - outlier_threshold / 100)
                upper_bound = median_price * (1 + outlier_threshold / 100)
                
                # Filter prices
                filtered_prices = [p for p in prices if lower_bound <= p <= upper_bound]
                
                if filtered_prices:
                    prices = filtered_prices
                    print(f"   🎯 Filtered outliers: kept {len(prices)} / {len(final_listings)} items")
            
            # Calculate price history by month for graph
            price_history = {}
            for listing in final_listings:
                if listing['price'] in prices:  # Only include non-outliers
                    # Extract YYYY-MM from date
                    month = listing['sold_date'][:7]  # "2024-12" from "2024-12-15"
                    if month not in price_history:
                        price_history[month] = []
                    price_history[month].append(listing['price'])
            
            # Calculate average per month
            monthly_avg = {}
            for month, month_prices in sorted(price_history.items()):
                monthly_avg[month] = int(sum(month_prices) / len(month_prices))
            
            stats = {
                'avg_price': int(sum(prices) / len(prices)),
                'min_price': int(min(prices)),
                'max_price': int(max(prices)),
                'listing_count': len(prices),
                'total_found': len(final_listings),
                'price_history': monthly_avg  # For the graph!
            }
            
            # FLAG suspicious items for manual review
            avg_price = stats['avg_price']
            flagged_items = []
            suspicious_keywords = ['lot', 'bundle', 'for parts', 'jeux', 'games']
            
            for listing in final_listings:
                flags = []
                
                # Flag if price is way too high
                if listing['price'] > avg_price * 2:
                    flags.append(f"PRICE_TOO_HIGH ({listing['price']}€ vs avg {avg_price}€)")
                
                # Flag if price is suspiciously low
                if listing['price'] < avg_price * 0.3:
                    flags.append(f"PRICE_TOO_LOW ({listing['price']}€ vs avg {avg_price}€)")
                
                # Flag suspicious keywords
                title_lower = listing['title'].lower()
                found_keywords = [kw for kw in suspicious_keywords if kw in title_lower]
                if found_keywords:
                    flags.append(f"SUSPICIOUS_KEYWORDS: {', '.join(found_keywords)}")
                
                if flags:
                    flagged_items.append({
                        'title': listing['title'],
                        'price': listing['price'],
                        'url': listing['url'],
                        'flags': flags
                    })
            
            if flagged_items:
                print(f"   ⚠️  {len(flagged_items)} suspicious items flagged for review")
                # Save flagged items to separate file
                flagged_file = f'flagged_{variant_key}.json'
                with open(flagged_file, 'w', encoding='utf-8') as f:
                    json.dump(flagged_items, f, indent=2, ensure_ascii=False)
                print(f"   💾 Saved to {flagged_file}")
        else:
            stats = {
                'avg_price': 0,
                'min_price': 0,
                'max_price': 0,
                'listing_count': 0,
                'total_found': 0,
                'price_history': {}
            }
        
        # Check minimum listings requirement
        min_required = self.config.get('scraping', {}).get('min_listings_required', 10)
        if stats['listing_count'] < min_required:
            print(f"   ⚠️  Warning: Only {stats['listing_count']} listings (min {min_required} recommended)")
        
        print(f"💰 Avg: {stats['avg_price']}€, Min: {stats['min_price']}€, Max: {stats['max_price']}€")
        print(f"📈 Price history: {len(stats['price_history'])} months of data")
        
        return {
            'variant_key': variant_key,
            'variant_name': variant_data['name'],
            'description': variant_data['description'],
            'stats': stats,
            'listings': final_listings
        }
    
    def scrape_all_variants(self):
        """Scrape all variants defined in config"""
        results = {}
        
        variants = self.config['variants']
        total = len(variants)
        
        print(f"\n🚀 Starting scrape for {total} variants...\n")
        
        for idx, (variant_key, variant_data) in enumerate(variants.items(), 1):
            print(f"\n[{idx}/{total}] Processing {variant_key}...")
            
            result = self.scrape_variant(variant_key, variant_data)
            results[variant_key] = result
            
            # Save progress after each variant
            self._save_progress(results)
            
            # Longer pause between variants to avoid rate limiting
            if idx < total:
                wait_time = random.uniform(8, 12)  # Random 8-12 seconds
                print(f"\n⏸️  Waiting {wait_time:.1f} seconds before next variant...")
                time.sleep(wait_time)
        
        print(f"\n{'='*60}")
        print(f"✅ Scraping complete! {total} variants processed.")
        print(f"{'='*60}\n")
        
        return results
    
    def scrape_all_complete(self, search_term="game boy color"):
        """
        NEW APPROACH: Scrape ALL available sold listings (90-day eBay limit)
        Much simpler since eBay only shows ~285 total results for Game Boy Color
        
        Args:
            search_term: Generic search term to use (default: "game boy color")
        
        Returns:
            Dictionary with all found listings organized by detected variants
        """
        print(f"\n{'='*60}")
        print(f"📊 COMPLETE SCRAPING APPROACH")
        print(f"🎯 Getting ALL sold '{search_term}' listings")
        print(f"📅 eBay's 90-day limit (~285 total expected)")
        print(f"{'='*60}")
        
        # Get all available listings
        all_listings = self.scrape_recent_complete(search_term)
        
        if not all_listings:
            print("❌ No listings found!")
            return {}
        
        # Sort by date (newest first)  
        all_listings.sort(key=lambda x: x['sold_date'], reverse=True)
        
        print(f"\n📋 Organizing {len(all_listings)} listings by detected variants...")
        
        # Load variant detection patterns from config
        variants = self.config.get('variants', {})
        
        # Organize listings by variant
        variant_results = {}
        
        # Initialize all known variants
        for variant_key, variant_data in variants.items():
            variant_results[variant_key] = {
                'variant_key': variant_key,
                'variant_name': variant_data['name'],
                'description': variant_data['description'],
                'stats': {
                    'avg_price': 0,
                    'min_price': 0,
                    'max_price': 0,
                    'listing_count': 0,
                    'total_found': 0,
                    'price_history': {}
                },
                'listings': []
            }
        
        # Add "unmatched" category for items that don't match any variant
        variant_results['unmatched'] = {
            'variant_key': 'unmatched',
            'variant_name': 'Unmatched Items',
            'description': 'Items that could not be matched to a specific variant',
            'stats': {
                'avg_price': 0,
                'min_price': 0,
                'max_price': 0,
                'listing_count': 0,
                'total_found': 0,
                'price_history': {}
            },
            'listings': []
        }
        
        # Classify each listing by variant
        for listing in all_listings:
            title_lower = listing['title'].lower()
            matched_variant = None
            
            # Try to match against each variant's search terms
            for variant_key, variant_data in variants.items():
                search_terms = variant_data.get('search_terms', [])
                
                # Check if any search term matches the title
                for search_term in search_terms:
                    # Extract color/variant name from search term
                    search_lower = search_term.lower()
                    
                    # Simple keyword matching (can be enhanced later)
                    if any(keyword in title_lower for keyword in search_lower.split() if len(keyword) > 3):
                        matched_variant = variant_key
                        break
                
                if matched_variant:
                    break
            
            # Add to appropriate variant
            target_variant = matched_variant or 'unmatched'
            variant_results[target_variant]['listings'].append(listing)
        
        # Calculate statistics for each variant
        for variant_key, variant_data in variant_results.items():
            listings = variant_data['listings']
            
            if listings:
                prices = [l['price'] for l in listings]
                
                # Remove outliers (±30% from median)
                if len(prices) >= 5:
                    median_price = sorted(prices)[len(prices) // 2]
                    lower_bound = median_price * 0.7
                    upper_bound = median_price * 1.3
                    filtered_prices = [p for p in prices if lower_bound <= p <= upper_bound]
                    if filtered_prices:
                        prices = filtered_prices
                
                # Calculate price history by month
                price_history = {}
                for listing in listings:
                    if listing['price'] in prices:
                        month = listing['sold_date'][:7]  # "2024-12" from "2024-12-15"
                        if month not in price_history:
                            price_history[month] = []
                        price_history[month].append(listing['price'])
                
                # Calculate monthly averages
                monthly_avg = {}
                for month, month_prices in sorted(price_history.items()):
                    monthly_avg[month] = int(sum(month_prices) / len(month_prices))
                
                variant_data['stats'] = {
                    'avg_price': int(sum(prices) / len(prices)),
                    'min_price': int(min(prices)),
                    'max_price': int(max(prices)),
                    'listing_count': len(prices),
                    'total_found': len(listings),
                    'price_history': monthly_avg
                }
                
                print(f"   {variant_key}: {len(listings)} listings, avg {variant_data['stats']['avg_price']}€")
        
        # Remove empty variants
        variant_results = {k: v for k, v in variant_results.items() if v['listings']}

        print(f"\n✅ Weekly scraping complete!")
        print(f"   📊 Variants found: {len(variant_results)}")
        print(f"   📈 Total listings organized: {sum(len(v['listings']) for v in variant_results.values())}")

        # Save results to file
        self._save_progress(variant_results)

        return variant_results
    
    def _save_progress(self, results):
        """Save current results to file"""
        output_file = 'scraped_data.json'
        with open(output_file, 'w', encoding='utf-8') as f:
            json.dump(results, f, ensure_ascii=False, indent=2)
        print(f"💾 Progress saved to {output_file}")


def main():
    """Main execution"""
    print("="*60)
    print("🎮 PrixRetro - eBay.fr Scraper")
    print("="*60)
    
    # Check if config exists
    import os
    if not os.path.exists('config.json'):
        print("\n❌ Error: config.json not found!")
        print("Please create config.json first.")
        print("See SCRAPER_GUIDE.md for instructions.")
        return
    
    try:
        scraper = EbayScraper('config.json')
        
        print("\n🚀 SCRAPING STRATEGY:")
        print("   📊 Getting ALL available sold listings (eBay's 90-day limit)")
        print("   🎯 ~285 total Game Boy Color results expected")
        print("   ✅ Complete data set, better date accuracy")
        print()
        
        # Use the new complete approach
        results = scraper.scrape_all_complete(search_term="game boy color")
        
        print("\n✅ Scraping completed successfully!")
        print(f"📁 Data saved to: scraped_data.json")
        print(f"\n📊 Summary:")
        
        total_listings = sum(r['stats']['listing_count'] for r in results.values())
        print(f"   • Variants found: {len(results)}")
        print(f"   • Total listings: {total_listings}")
        print(f"   • Average per variant: {total_listings // len(results) if results else 0}")
        
        # Show breakdown by variant
        print(f"\n📋 Breakdown by variant:")
        for variant_key, data in results.items():
            count = data['stats']['listing_count']
            avg_price = data['stats']['avg_price']
            print(f"   • {variant_key}: {count} listings, avg {avg_price}€")
        
        print("\n🎯 Next step: Run update_site.py to generate HTML pages")
        
    except Exception as e:
        print(f"\n❌ Error during scraping: {e}")
        import traceback
        traceback.print_exc()


if __name__ == "__main__":
    main()
