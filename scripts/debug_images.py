#!/usr/bin/env python3
"""Debug script to see what images are on the page"""

import requests
from bs4 import BeautifulSoup
from urllib.parse import urljoin

url = "https://altarofgaming.com/game-boy-color-models-color-variations-limited-editions/"

print(f"Fetching: {url}\n")

session = requests.Session()
session.headers.update({
    'User-Agent': 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
})

response = session.get(url, timeout=30)
soup = BeautifulSoup(response.content, 'html.parser')

# Check all images
all_imgs = soup.find_all('img')
print(f"📊 Total <img> tags found: {len(all_imgs)}\n")

# Check for background-image in style attributes
bg_images = soup.find_all(attrs={'style': lambda x: x and 'background-image' in x})
print(f"📊 Elements with background-image in style: {len(bg_images)}\n")

# Sample first 10 images
print("=" * 60)
print("FIRST 10 <img> TAGS:")
print("=" * 60)
for i, img in enumerate(all_imgs[:10], 1):
    src = img.get('src', 'NONE')
    print(f"\n{i}.")
    print(f"   src:           {src[:80] if src != 'NONE' else 'NONE'}")
    print(f"   data-src:      {img.get('data-src', 'NONE')[:80]}")
    print(f"   data-lazy-src: {img.get('data-lazy-src', 'NONE')[:80]}")
    print(f"   width:         {img.get('width', 'auto')}")
    print(f"   height:        {img.get('height', 'auto')}")
    print(f"   class:         {img.get('class', [])}")
    print(f"   alt:           {img.get('alt', 'NONE')[:60]}")

# Check what filter logic would apply
print("\n" + "=" * 60)
print("FILTERING SIMULATION:")
print("=" * 60)

passed = 0
failed_reason = {}
seen_urls = set()

for img in all_imgs:
    # Get URL with proper fallback chain (FIXED)
    img_url = img.get('src') or img.get('data-src') or img.get('data-lazy-src')

    # Try data-srcset as last resort
    if not img_url and img.get('data-srcset'):
        srcset = img.get('data-srcset', '').split(',')
        if srcset:
            img_url = srcset[0].strip().split()[0]

    if not img_url:
        failed_reason['No URL'] = failed_reason.get('No URL', 0) + 1
        continue

    if img_url in seen_urls:
        failed_reason['Duplicate'] = failed_reason.get('Duplicate', 0) + 1
        continue

    seen_urls.add(img_url)

    if img_url.startswith('data:'):
        failed_reason['Data URI'] = failed_reason.get('Data URI', 0) + 1
        continue

    # Check size
    width = img.get('width')
    height = img.get('height')
    if width and height:
        try:
            # Remove 'px' if present
            width_num = int(str(width).replace('px', ''))
            height_num = int(str(height).replace('px', ''))
            if width_num < 200 or height_num < 200:
                failed_reason['Too small'] = failed_reason.get('Too small', 0) + 1
                continue
        except ValueError:
            pass

    # Check skip keywords
    skip_keywords = [
        'logo', 'icon', 'avatar', 'button', 'banner',
        'favicon', 'sprite', 'ui-', 'social',
        'author', 'profile', 'ad-', 'placeholder',
        'menu', 'nav', 'sidebar', 'thumb', 'thumbnail',
        'footer', 'header', 'widget', 'comment',
        '-150x', '-96x96', '-50x50', '-32x32', '-24x24'
    ]

    skipped = False
    for keyword in skip_keywords:
        if keyword in img_url.lower():
            failed_reason[f'Keyword: {keyword}'] = failed_reason.get(f'Keyword: {keyword}', 0) + 1
            skipped = True
            break

    if skipped:
        continue

    # Skip WordPress plugin/theme images
    if any(x in img_url.lower() for x in ['/plugins/', '/themes/', '/avatar/']):
        failed_reason['WordPress system'] = failed_reason.get('WordPress system', 0) + 1
        continue

    # Would pass!
    passed += 1
    if passed <= 5:
        print(f"\n✓ PASS #{passed}:")
        print(f"  URL: {img_url[:80]}")
        print(f"  Alt: {img.get('alt', 'none')[:60]}")

print(f"\n📊 RESULTS:")
print(f"   Would download: {passed}")
print(f"\n   Failed filters:")
for reason, count in sorted(failed_reason.items(), key=lambda x: -x[1]):
    print(f"     - {reason}: {count}")

print("\n" + "=" * 60)
