#!/usr/bin/env python3
"""
Download ALL images from eBay listings for ML training

This script:
1. Reads your sorted/labeled data (e.g., scraped_data.json, scraped_data_gba.json)
2. For each item, visits the eBay listing page
3. Downloads ALL images (not just the thumbnail)
4. Saves to organized folders: data/images/{console}/{variant}/
5. Updates JSON with local image paths

Usage:
    python3 download_listing_images.py scraped_data.json game-boy-color
    python3 download_listing_images.py scraped_data_gba.json game-boy-advance
"""

import json
import os
import requests
import time
from pathlib import Path
from bs4 import BeautifulSoup
from urllib.parse import urljoin

# Base directory for images
BASE_IMAGE_DIR = Path('data/images')


def get_all_images_from_listing(item_url, session):
    """
    Visit eBay listing page and extract ALL image URLs

    Returns:
        List of image URLs (high resolution)
    """
    try:
        response = session.get(item_url, timeout=10)
        response.raise_for_status()

        soup = BeautifulSoup(response.text, 'html.parser')

        image_urls = []

        # Method 1: Try finding the image carousel/gallery
        # eBay uses different selectors, try multiple approaches

        # Look for high-res images in carousel
        carousel_images = soup.select('img[src*="s-l1600"], img[src*="s-l500"]')
        for img in carousel_images:
            src = img.get('src', '')
            if src and 's-l' in src:
                # Upgrade to highest resolution (s-l1600)
                src = src.replace('s-l500', 's-l1600').replace('s-l400', 's-l1600')
                image_urls.append(src)

        # Method 2: Look for data-zoom-src (high-res versions)
        zoom_images = soup.select('img[data-zoom-src]')
        for img in zoom_images:
            src = img.get('data-zoom-src', '')
            if src:
                image_urls.append(src)

        # Method 3: Look in JavaScript data (eBay embeds image array)
        scripts = soup.find_all('script')
        for script in scripts:
            if script.string and '"maxImageUrl"' in script.string:
                # Extract image URLs from JavaScript
                import re
                matches = re.findall(r'"maxImageUrl":"([^"]+)"', script.string)
                for match in matches:
                    # Unescape URL
                    url = match.replace('\\/', '/')
                    if url not in image_urls:
                        image_urls.append(url)

        # Remove duplicates while preserving order
        seen = set()
        unique_urls = []
        for url in image_urls:
            if url not in seen:
                seen.add(url)
                unique_urls.append(url)

        print(f"    Found {len(unique_urls)} images on listing page")
        return unique_urls

    except Exception as e:
        print(f"    ⚠️  Error fetching listing: {e}")
        return []


def download_image(image_url, save_path, session):
    """
    Download an image and save to disk

    Returns:
        True if successful, False otherwise
    """
    try:
        response = session.get(image_url, timeout=10, stream=True)
        response.raise_for_status()

        # Create directory if needed
        save_path.parent.mkdir(parents=True, exist_ok=True)

        # Save image
        with open(save_path, 'wb') as f:
            for chunk in response.iter_content(chunk_size=8192):
                f.write(chunk)

        return True

    except Exception as e:
        print(f"    ⚠️  Error downloading image: {e}")
        return False


def process_console_data(json_file, console_name):
    """
    Process a JSON file and download all images

    Args:
        json_file: Path to scraped_data.json
        console_name: e.g., 'game-boy-color', 'game-boy-advance'
    """
    print(f"\n{'='*60}")
    print(f"Processing: {json_file}")
    print(f"Console: {console_name}")
    print(f"{'='*60}\n")

    # Load data
    with open(json_file, 'r', encoding='utf-8') as f:
        data = json.load(f)

    # Create session with headers
    session = requests.Session()
    session.headers.update({
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'fr-FR,fr;q=0.9,en-US;q=0.8,en;q=0.7',
    })

    total_items = 0
    total_images = 0
    skipped_items = 0

    # Iterate through variants
    for variant_key, variant_data in data.items():
        if not isinstance(variant_data, dict) or 'items' not in variant_data:
            continue

        print(f"\n📁 Processing variant: {variant_key}")
        print(f"   Items: {len(variant_data['items'])}")

        # Create variant directory
        variant_dir = BASE_IMAGE_DIR / console_name / variant_key
        variant_dir.mkdir(parents=True, exist_ok=True)

        # Process each item
        for item in variant_data['items']:
            item_id = item.get('item_id')
            item_url = item.get('url', '')

            if not item_id or not item_url:
                print(f"  ⚠️  Skipping item (no ID or URL)")
                skipped_items += 1
                continue

            print(f"\n  📦 Item {item_id}")

            # Check if already downloaded
            existing_images = list(variant_dir.glob(f"{item_id}_*.jpg"))
            if existing_images:
                print(f"    ✅ Already downloaded ({len(existing_images)} images), skipping")
                # Add to item metadata if not already there
                if 'local_images' not in item:
                    item['local_images'] = [str(img.relative_to(Path.cwd())) for img in existing_images]
                    total_images += len(existing_images)
                continue

            # Get all image URLs from listing page
            image_urls = get_all_images_from_listing(item_url, session)

            if not image_urls:
                print(f"    ⚠️  No images found, skipping")
                skipped_items += 1
                time.sleep(1)  # Rate limiting
                continue

            # Download each image
            local_images = []
            for idx, image_url in enumerate(image_urls, start=1):
                save_path = variant_dir / f"{item_id}_img{idx}.jpg"

                print(f"    ⬇️  Downloading image {idx}/{len(image_urls)}...")

                if download_image(image_url, save_path, session):
                    local_images.append(str(save_path.relative_to(Path.cwd())))
                    total_images += 1

                time.sleep(0.5)  # Rate limiting between images

            # Update item with local image paths
            item['local_images'] = local_images

            print(f"    ✅ Downloaded {len(local_images)} images")
            total_items += 1

            # Rate limiting between items
            time.sleep(2)

    # Save updated JSON with image paths
    output_file = json_file.replace('.json', '_with_images.json')
    with open(output_file, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False, indent=2)

    print(f"\n{'='*60}")
    print(f"✅ DONE!")
    print(f"{'='*60}")
    print(f"Items processed: {total_items}")
    print(f"Images downloaded: {total_images}")
    print(f"Items skipped: {skipped_items}")
    print(f"Average images per item: {total_images / total_items if total_items > 0 else 0:.1f}")
    print(f"\nUpdated JSON saved to: {output_file}")
    print(f"Images saved to: {BASE_IMAGE_DIR / console_name}/")
    print(f"\n💡 You can now use these images for ML training!")


if __name__ == '__main__':
    import sys

    if len(sys.argv) < 3:
        print("Usage: python3 download_listing_images.py <json_file> <console_name>")
        print("\nExamples:")
        print("  python3 download_listing_images.py scraped_data.json game-boy-color")
        print("  python3 download_listing_images.py scraped_data_gba.json game-boy-advance")
        sys.exit(1)

    json_file = sys.argv[1]
    console_name = sys.argv[2]

    if not os.path.exists(json_file):
        print(f"❌ Error: File not found: {json_file}")
        sys.exit(1)

    process_console_data(json_file, console_name)
