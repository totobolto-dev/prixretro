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
    
    def search_sold_listings(self, search_term, max_results=None, full_history=False):
        """
        Search for sold listings on eBay.fr
        
        Args:
            search_term: Search query (e.g., "game boy color violet")
            max_results: Maximum number of results per page (default from config)
            full_history: If True, paginate through ALL pages (can be 1000+ items!)
        
        Returns:
            List of dictionaries with listing data
        """
        if max_results is None:
            max_results = self.config.get('scraping', {}).get('max_results_per_search', 60)
        
        all_listings = []
        page = 1
        # SCRAPING STRATEGY:
        # - full_history=True: Try to get ALL history (may hit CAPTCHA frequently)
        # - Realistic: 5 pages = 1000 items per session, run multiple times
        # - Aggressive: 20+ pages = complete history, but expect many CAPTCHA pauses
        max_pages = self.config.get('scraping', {}).get('max_pages_full_history', 999) if full_history else 1
        items_per_page = 200  # eBay max
        skip_count = 0
        
        while page <= max_pages:
            retry_same_page = False  # Flag for CAPTCHA retry
            print(f"🔍 Searching: '{search_term}' (page {page}/{max_pages if full_history else 1}, skip={skip_count})")
            
            # eBay.fr sold items URL with SKIP COUNT PAGINATION
            # _ipg: Items per page (200 max)
            # _skc: Skip count (0, 200, 400, etc.)
            encoded_term = quote_plus(search_term)
            url = (
                f"https://www.ebay.fr/sch/i.html?"
                f"_nkw={encoded_term}&"
                f"_dcat=139971&"
                f"Mod%C3%A8le=Nintendo%20Game%20Boy%20Color&"
                f"Plateforme=Nintendo%20Game%20Boy%20Color&"
                f"LH_Sold=1&"
                f"LH_Complete=1&"
                f"_sop=13&"
                f"_ipg={items_per_page}&"
                f"_skc={skip_count}"
            )
            
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
                            listings.append(listing)
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
                
                print(f"✅ Parsed {len(listings)} valid listings from page {page}")
                
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
                    skip_count += items_per_page  # Skip to next batch
                
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
            print(f"      ❌ REJECT (no gameboy): {title[:60]}")
            return None
        
        # REJECT broken/defective consoles - Use precise phrases
        broken_keywords = [
            ' hs', 'h.s.', 'hors service', 'ne fonctionne', 'defectueux', 'défectueux',
            'ne marche', 'broken', 'not working', 'for parts', 'pour pieces', 'pour pièces',
            'pour piece', 'pour pièce', 'à réparer', 'a reparer',
            'dead', 'mort', 'ne s\'allume', 'ne s allume',
            'does not work', 'ne charge pas', 'pas testé', 'pas teste', 'untested',
            'pour reparation', 'for repair', 'non fonctionnel', 'non testé',
            'no sound', 'pas de son', 'sans son',  # No sound = broken
            'no power', 'pas d\'image', 'no display'  # Other defects
        ]
        
        # Check for broken/defective keywords
        for keyword in broken_keywords:
            if keyword in title_lower:
                print(f"      ❌ REJECT (broken: '{keyword}'): {title[:60]}")
                return None
        
        # REJECT parts and accessories - Use precise phrases only
        parts_keywords = [
            'boîte vide', 'boite vide',  # Empty box
            'sans console',  # Box without console
            'câble link', 'cable link', 'link cable',  # Link cable
            'chargeur', 'adaptateur',  # Charger
            'kit condensateur', 'kit réparation',  # Repair kits
            'pièce détachée', 'piece detachee', 'pièces détachées',
            'condensateur', 'capacitor',
            'nappe', 'ribbon cable',
            'carte mère', 'carte mere', 'motherboard', 'pcb',
            'coque seule', 'coque neuve', 'shell only', 'housing only',  # Shell only
            'cache pile', 'battery cover', 'couvercle pile',  # Battery covers
            'vis seules', 'screw set',  # Screws only
            'écran seul', 'screen only', 'lens only',  # Screen only
            'boutons seuls', 'button set',  # Buttons only
            'sticker seul', 'decal only',
            'notice seule', 'manuel seul',  # Manual only
            'cartouche seule',  # Cartridge only
            'remplacement écran', 'replacement screen',
            'rubber pad', 'pad contact', 'caoutchouc',  # Rubber contacts
            'lot de', 'bundle',  # Bundles/lots (often suspicious)
        ]
        
        # Check for parts/accessories keywords
        for keyword in parts_keywords:
            if keyword in title_lower:
                print(f"      ❌ REJECT (parts: '{keyword}'): {title[:60]}")
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
            import re
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
        
        for elem in item.find_all(['span', 'div']):
            text = elem.get_text(strip=True)
            if 'Vendu le' in text or 'Vendu ' in text:
                has_vendu = True
                # Try to extract the date part
                import re
                date_match = re.search(r'(\d{1,2})\s+(\w+)\.?\s+(\d{4})', text)
                if date_match:
                    day, month_fr, year = date_match.groups()
                    months_fr = {
                        'janv': '01', 'févr': '02', 'mars': '03', 'avr': '04',
                        'mai': '05', 'juin': '06', 'juil': '07', 'août': '08',
                        'sept': '09', 'oct': '10', 'nov': '11', 'déc': '12'
                    }
                    month_num = months_fr.get(month_fr.lower(), '01')
                    # Format: YYYY-MM-DD for easy sorting and grouping
                    date_sold = f"{year}-{month_num}-{day.zfill(2)}"
                break
        
        # CRITICAL: Skip items not actually sold
        if not has_vendu:
            print(f"      ❌ REJECT (not sold): {title[:60]}")
            return None
        
        if not date_sold:
            date_sold = datetime.now().strftime("%Y-%m-%d")
        
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
                import re
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
        results = scraper.scrape_all_variants()
        
        print("\n✅ Scraping completed successfully!")
        print(f"📁 Data saved to: scraped_data.json")
        print(f"\n📊 Summary:")
        
        total_listings = sum(r['stats']['listing_count'] for r in results.values())
        print(f"   • Variants scraped: {len(results)}")
        print(f"   • Total listings: {total_listings}")
        print(f"   • Average per variant: {total_listings // len(results) if results else 0}")
        
        print("\n🎯 Next step: Run update_site.py to generate HTML pages")
        
    except Exception as e:
        print(f"\n❌ Error during scraping: {e}")
        import traceback
        traceback.print_exc()


if __name__ == "__main__":
    main()
