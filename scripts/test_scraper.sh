#!/bin/bash
# Quick test to verify the scraper finds pages

echo "🧪 Testing Altar of Gaming scraper discovery..."
echo ""

python3 - << 'EOF'
import requests
from bs4 import BeautifulSoup

BASE_URL = "https://altarofgaming.com"
DISCOVERY_URL = "https://altarofgaming.com/all-console-models/"

try:
    print(f"Fetching: {DISCOVERY_URL}")
    response = requests.get(DISCOVERY_URL, timeout=30)
    response.raise_for_status()

    soup = BeautifulSoup(response.content, 'html.parser')

    # Find aog-box-item links
    links = soup.find_all('a', class_='aog-box-item')

    model_pages = []
    for link in links:
        href = link.get('href')
        h6 = link.find('h6')

        if href and h6:
            title = h6.get_text(strip=True)
            # Filter for model/variant pages
            if any(k in href.lower() for k in ['models', 'controllers', 'color-variations', 'limited-editions']):
                model_pages.append((title, href))

    print(f"\n✅ Found {len(model_pages)} model/variant pages")
    print("\nSample pages:")
    for title, url in model_pages[:10]:
        print(f"  • {title}")
        print(f"    {url}")

    if len(model_pages) > 10:
        print(f"\n  ... and {len(model_pages) - 10} more pages")

    print("\n✅ Scraper should work correctly!")

except Exception as e:
    print(f"\n❌ Error: {e}")
    print("Check your internet connection or the site might be down.")

EOF
