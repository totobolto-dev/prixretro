#!/usr/bin/env python3
"""
Scrape console images from altarofgaming.com
Uses hard-coded list of model/variant pages
"""

import os
import re
import sys
import time
import requests
from urllib.parse import urljoin, urlparse
from pathlib import Path
from bs4 import BeautifulSoup

# Output directory
OUTPUT_DIR = "scraped_images"

# Hard-coded console model pages (from your list)
CONSOLE_PAGES = {
    "neo-geo-pocket-color": {
        "url": "https://altarofgaming.com/neo-geo-pocket-color-models-color-variations-limited-editions/",
        "title": "NEO GEO Pocket Color Models"
    },
    "nintendo-3ds": {
        "url": "https://altarofgaming.com/nintendo-3ds-models-color-variations-limited-editions/",
        "title": "Nintendo 3DS Models"
    },
    "nintendo-64-controllers": {
        "url": "https://altarofgaming.com/nintendo-64-controllers-color-variations-limited-editions/",
        "title": "Nintendo 64 Controllers"
    },
    "nintendo-64": {
        "url": "https://altarofgaming.com/nintendo-64-models-color-variations-limited-editions/",
        "title": "Nintendo 64 Models"
    },
    "nintendo-ds": {
        "url": "https://altarofgaming.com/nintendo-ds-models-color-variations-limited-editions/",
        "title": "Nintendo DS Models"
    },
    "game-boy": {
        "url": "https://altarofgaming.com/nintendo-game-boy-models-color-variations-limited-editions/",
        "title": "Nintendo Game Boy Models"
    },
    "gamecube-controllers": {
        "url": "https://altarofgaming.com/nintendo-gamecube-controllers-color-variations-limited-editions/",
        "title": "Nintendo GameCube Controllers"
    },
    "gamecube": {
        "url": "https://altarofgaming.com/nintendo-gamecube-models-color-variations-limited-editions/",
        "title": "Nintendo GameCube Models"
    },
    "game-boy-advance": {
        "url": "https://altarofgaming.com/game-boy-advance-models-color-variations-limited-editions/",
        "title": "Nintendo GBA Models"
    },
    "game-boy-color": {
        "url": "https://altarofgaming.com/game-boy-color-models-color-variations-limited-editions/",
        "title": "Nintendo GBC Models"
    },
    "switch-controllers": {
        "url": "https://altarofgaming.com/nintendo-switch-joy-cons-pro-controllers-color-variations-limited-editions/",
        "title": "Nintendo Switch Controllers"
    },
    "switch": {
        "url": "https://altarofgaming.com/nintendo-switch-models-color-variations-limited-editions/",
        "title": "Nintendo Switch Models"
    },
    "ps-vita": {
        "url": "https://altarofgaming.com/playstation-vita-models-color-variations-limited-editions/",
        "title": "PS Vita Models"
    },
    "ps2-controllers": {
        "url": "https://altarofgaming.com/playstation-2-controllers-colors-limited-editions/",
        "title": "PS2 Controllers"
    },
    "ps2-memory-cards": {
        "url": "https://altarofgaming.com/playstation-2-memory-cards-colors-limited-editions/",
        "title": "PS2 Memory Cards"
    },
    "ps2-modchips": {
        "url": "https://altarofgaming.com/playstation-2-modchips/",
        "title": "PS2 Modchips"
    },
    "ps2": {
        "url": "https://altarofgaming.com/playstation-2-models-color-variations-limited-editions/",
        "title": "PS2 Models"
    },
    "ps3-controllers": {
        "url": "https://altarofgaming.com/playstation-3-controllers-color-variations-limited-editions/",
        "title": "PS3 Controllers"
    },
    "ps3": {
        "url": "https://altarofgaming.com/playstation-3-models-color-variations-limited-editions/",
        "title": "PS3 Models"
    },
    "ps4-controllers": {
        "url": "https://altarofgaming.com/playstation-4-controllers-color-variations-limited-editions/",
        "title": "PS4 Controllers"
    },
    "ps4": {
        "url": "https://altarofgaming.com/playstation-4-models-color-variations-limited-editions/",
        "title": "PS4 Models"
    },
    "ps5-controllers": {
        "url": "https://altarofgaming.com/playstation-5-controllers-color-variations-limited-editions/",
        "title": "PS5 Controllers"
    },
    "ps5": {
        "url": "https://altarofgaming.com/playstation-5-models-color-variations-limited-editions/",
        "title": "PS5 Models"
    },
    "psp": {
        "url": "https://altarofgaming.com/playstation-portable-models-color-variations-limited-editions/",
        "title": "PSP Models"
    },
    "dreamcast-controllers": {
        "url": "https://altarofgaming.com/sega-dreamcast-controllers-color-variations-limited-editions/",
        "title": "SEGA DreamCast Controllers"
    },
    "dreamcast": {
        "url": "https://altarofgaming.com/sega-dreamcast-models-color-variations-limited-editions/",
        "title": "SEGA DreamCast Models"
    },
    "game-gear": {
        "url": "https://altarofgaming.com/sega-game-gear-models-color-variations-limited-editions/",
        "title": "SEGA Game Gear Models"
    },
    "genesis-mega-drive": {
        "url": "https://altarofgaming.com/sega-genesis-mega-drive-models-color-variations-limited-editions/",
        "title": "SEGA Genesis / Mega Drive Models"
    },
    "saturn-controllers": {
        "url": "https://altarofgaming.com/sega-saturn-controllers-color-variations-limited-editions/",
        "title": "SEGA Saturn Controllers"
    },
    "saturn": {
        "url": "https://altarofgaming.com/sega-saturn-models-color-variations-limited-editions/",
        "title": "SEGA Saturn Models"
    },
    "neo-geo-pocket": {
        "url": "https://altarofgaming.com/neo-geo-pocket-models-color-variations-limited-editions/",
        "title": "SNK Neo Geo Pocket Models"
    },
    "xbox-one-controllers": {
        "url": "https://altarofgaming.com/xbox-one-controllers-color-variations-limited-editions/",
        "title": "Xbox One Controllers"
    },
}

def get_session():
    """Create requests session with headers and retry logic"""
    from requests.adapters import HTTPAdapter
    from urllib3.util.retry import Retry

    session = requests.Session()
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-US,en;q=0.9',
        'Accept-Encoding': 'gzip, deflate, br',
        'Connection': 'keep-alive',
    })

    # Add retry strategy
    retry_strategy = Retry(
        total=3,
        backoff_factor=2,
        status_forcelist=[429, 500, 502, 503, 504],
    )
    adapter = HTTPAdapter(max_retries=retry_strategy)
    session.mount("http://", adapter)
    session.mount("https://", adapter)

    return session

def sanitize_filename(filename):
    """Remove invalid characters from filename"""
    return re.sub(r'[<>:"/\\|?*]', '_', filename)

def download_image(session, url, save_path):
    """Download single image"""
    try:
        response = session.get(url, timeout=30, stream=True)
        response.raise_for_status()

        with open(save_path, 'wb') as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)

        print(f"  ✓ Downloaded: {save_path.name}")
        return True
    except Exception as e:
        print(f"  ✗ Failed: {url} - {e}")
        return False

def scrape_console_page(session, console_name, page_info):
    """Scrape images from a console model/variant page"""
    page_url = page_info['url']
    console_title = page_info['title']

    print(f"\n🎮 Scraping: {console_title}")
    print(f"   URL: {page_url}")

    # Try with retries
    max_attempts = 3
    response = None
    for attempt in range(1, max_attempts + 1):
        try:
            if attempt > 1:
                print(f"   Retry attempt {attempt}/{max_attempts}...")
            response = session.get(page_url, timeout=60)
            response.raise_for_status()
            break
        except requests.exceptions.Timeout:
            print(f"  ⚠ Timeout on attempt {attempt}")
            if attempt < max_attempts:
                wait_time = attempt * 5
                print(f"  ⏳ Waiting {wait_time}s...")
                time.sleep(wait_time)
            else:
                print(f"  ✗ Failed after {max_attempts} attempts")
                return 0
        except Exception as e:
            print(f"  ✗ Error: {e}")
            if attempt < max_attempts:
                time.sleep(5)
            else:
                return 0

    if not response:
        return 0

    soup = BeautifulSoup(response.content, 'html.parser')

    # Create console folder
    console_dir = Path(OUTPUT_DIR) / sanitize_filename(console_name)
    console_dir.mkdir(parents=True, exist_ok=True)

    # Collect image URLs from multiple sources
    image_sources = []

    # Strategy: Only grab images that are clearly console photos
    # 1. Images with gallery classes (lightGallery)
    gallery_imgs = soup.find_all('img', class_=lambda x: x and any(cls in str(x) for cls in ['lg-object', 'lg-image', 'gallery']))
    print(f"  📊 Found {len(gallery_imgs)} gallery images (lg-object/lg-image)")

    # 2. Images with 'attachment-full' class (WordPress full-size images)
    full_size_imgs = soup.find_all('img', class_=lambda x: x and 'attachment-full' in str(x))
    print(f"  📊 Found {len(full_size_imgs)} full-size attachment images")

    # 3. Images within article/main content areas
    content_areas = soup.find_all(['article', 'main', 'div'], class_=lambda x: x and any(c in str(x).lower() for c in ['content', 'entry', 'post', 'article']))
    content_imgs = []
    for area in content_areas:
        content_imgs.extend(area.find_all('img'))
    print(f"  📊 Found {len(content_imgs)} images in content areas")

    # Combine all (dedupe happens later)
    all_relevant_imgs = gallery_imgs + full_size_imgs + content_imgs
    print(f"  📊 Total relevant images to check: {len(all_relevant_imgs)}")

    # Add to sources
    for img in all_relevant_imgs:
        image_sources.append(('img', img, None))

    print(f"  📊 Total image sources to process: {len(image_sources)}")

    downloaded = 0
    seen_urls = set()

    for idx, (source_type, source_data, extra) in enumerate(image_sources, 1):
        # Get image URL based on source type
        if source_type == 'background':
            img_url = source_data
            alt_text = ''
        else:
            # It's an <img> tag
            img = source_data

            # Get URL with proper fallback chain
            img_url = img.get('src') or img.get('data-src') or img.get('data-lazy-src')

            # Try data-srcset as last resort
            if not img_url and img.get('data-srcset'):
                srcset = img.get('data-srcset', '').split(',')
                if srcset:
                    img_url = srcset[0].split()[0]

            alt_text = img.get('alt', '')

            # Skip tiny images (only for <img> tags)
            width = img.get('width')
            height = img.get('height')
            if width and height:
                try:
                    if int(width) < 200 or int(height) < 200:
                        continue
                except ValueError:
                    pass

        if not img_url or img_url in seen_urls:
            continue

        seen_urls.add(img_url)

        # Skip data URIs
        if img_url.startswith('data:'):
            continue

        # Must be from assets.altarofgaming.com/wp-content/uploads (the CDN for actual content)
        if 'assets.altarofgaming.com/wp-content/uploads' not in img_url:
            continue

        # Skip non-console images
        skip_keywords = [
            'logo', 'icon', 'avatar', 'button', 'banner',
            'favicon', 'sprite', 'ui-', 'social',
            'author', 'profile', 'ad-', 'placeholder',
            '-150x', '-96x96', '-50x50', '-32x32', '-24x24'  # Thumbnails
        ]
        if any(skip in img_url.lower() for skip in skip_keywords):
            continue

        # Skip WordPress system folders
        if any(x in img_url.lower() for x in ['/plugins/', '/themes/', '/avatar/']):
            continue

        # Prefer high-quality images (remove size suffixes)
        for size in ['-300x169', '-300x154', '-300x188', '-768x432', '-1024x576']:
            img_url = img_url.replace(size, '')

        # Make absolute URL
        img_url = urljoin(page_url, img_url)

        # Get original filename from URL
        parsed = urlparse(img_url)
        original_name = Path(parsed.path).name

        # If no filename in URL, generate one
        if not original_name or original_name == '/':
            ext = Path(parsed.path).suffix or '.jpg'
            if alt_text:
                filename = f"{downloaded+1:03d}_{sanitize_filename(alt_text)}{ext}"
            else:
                filename = f"{downloaded+1:03d}_image{ext}"
        else:
            # Use original filename with number prefix
            filename = f"{downloaded+1:03d}_{sanitize_filename(original_name)}"

        # Limit filename length
        if len(filename) > 200:
            name_part = filename[:195]
            ext = Path(filename).suffix
            filename = name_part + ext

        save_path = console_dir / filename

        # Skip if already exists
        if save_path.exists():
            print(f"  ⊘ Skipped (exists): {filename}")
            continue

        # Download
        if download_image(session, img_url, save_path):
            downloaded += 1
            time.sleep(0.5)  # Be polite

    print(f"  ✓ Total downloaded: {downloaded} images")
    return downloaded

def main():
    print("=" * 60)
    print("🎮 Altar of Gaming Console Image Scraper")
    print("   (Hard-coded Model & Variant Pages)")
    print("=" * 60)

    session = get_session()

    # Show what we'll scrape
    print(f"\n📥 Will scrape {len(CONSOLE_PAGES)} console pages:")
    for idx, (folder, info) in enumerate(list(CONSOLE_PAGES.items())[:10], 1):
        print(f"  {idx}. {info['title']}")
    if len(CONSOLE_PAGES) > 10:
        print(f"  ... and {len(CONSOLE_PAGES) - 10} more")

    confirm = input("\nContinue? [Y/n]: ").strip().lower()
    if confirm == 'n':
        print("Cancelled.")
        return

    total_downloaded = 0
    for console_name, page_info in CONSOLE_PAGES.items():
        count = scrape_console_page(session, console_name, page_info)
        total_downloaded += count
        time.sleep(2)  # Be polite between pages

    print("\n" + "=" * 60)
    print(f"✅ Scraping complete!")
    print(f"   Total images downloaded: {total_downloaded}")
    print(f"   Saved to: {OUTPUT_DIR}/")
    print("=" * 60)

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Interrupted by user")
        sys.exit(1)
