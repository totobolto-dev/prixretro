#!/usr/bin/env python3
"""
Generate XML sitemap for PrixRetro Game Boy Color price tracker
"""

import json
from datetime import datetime
import xml.etree.ElementTree as ET

def generate_sitemap():
    """Generate XML sitemap"""
    
    # Load variant data
    with open('scraped_data_clean.json', 'r', encoding='utf-8') as f:
        data = json.load(f)
    
    # Create sitemap root
    urlset = ET.Element('urlset')
    urlset.set('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9')
    urlset.set('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance')
    urlset.set('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd')
    
    base_url = 'https://www.prixretro.com'
    lastmod = datetime.now().strftime('%Y-%m-%d')
    
    # Homepage
    url = ET.SubElement(urlset, 'url')
    ET.SubElement(url, 'loc').text = f'{base_url}/'
    ET.SubElement(url, 'lastmod').text = lastmod
    ET.SubElement(url, 'changefreq').text = 'daily'
    ET.SubElement(url, 'priority').text = '1.0'
    
    # Variant pages
    for variant_key, variant_data in data.items():
        if isinstance(variant_data, dict):
            url = ET.SubElement(urlset, 'url')
            ET.SubElement(url, 'loc').text = f'{base_url}/game-boy-color-{variant_key}.html'
            ET.SubElement(url, 'lastmod').text = lastmod
            ET.SubElement(url, 'changefreq').text = 'weekly'
            ET.SubElement(url, 'priority').text = '0.8'
    
    # Create tree and format
    tree = ET.ElementTree(urlset)
    ET.indent(tree, space='  ')
    
    # Write sitemap
    tree.write('sitemap.xml', encoding='utf-8', xml_declaration=True)
    print('✅ Generated sitemap.xml')

def generate_robots_txt():
    """Generate robots.txt"""
    
    robots_content = """User-agent: *
Allow: /

# Sitemap
Sitemap: https://www.prixretro.com/sitemap.xml

# Block common crawlers we don't want
User-agent: AhrefsBot
Disallow: /

User-agent: MJ12bot
Disallow: /
"""
    
    with open('robots.txt', 'w') as f:
        f.write(robots_content)
    
    print('✅ Generated robots.txt')

if __name__ == "__main__":
    generate_sitemap()
    generate_robots_txt()
    print('\n🎯 SEO files ready:')
    print('   📄 sitemap.xml - for Google Search Console')
    print('   🤖 robots.txt - crawl instructions')
    print('\n🚀 Next steps:')
    print('   1. Add to website deployment')
    print('   2. Submit sitemap to Google Search Console')
    print('   3. Add structured data to pages')