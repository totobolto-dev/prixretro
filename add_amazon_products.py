#!/usr/bin/env python3
"""
Amazon Product Manager for PrixRetro

This script helps you:
1. Add Amazon products to your database
2. Generate affiliate links with your tag (prixretro-21)
3. Create HTML snippets for your website

Usage:
    # Add a new product
    python3 add_amazon_products.py add

    # Generate HTML for a console
    python3 add_amazon_products.py generate game-boy-color

    # List all products
    python3 add_amazon_products.py list
"""

import json
import sys
from pathlib import Path

PRODUCTS_FILE = 'amazon_products.json'
AFFILIATE_TAG = 'prixretro-21'


def load_products():
    """Load products from JSON file"""
    if not Path(PRODUCTS_FILE).exists():
        return {}

    with open(PRODUCTS_FILE, 'r', encoding='utf-8') as f:
        return json.load(f)


def save_products(products):
    """Save products to JSON file"""
    with open(PRODUCTS_FILE, 'w', encoding='utf-8') as f:
        json.dump(products, f, ensure_ascii=False, indent=2)


def generate_affiliate_link(asin):
    """Generate Amazon.fr affiliate link with your tag"""
    if asin == "REPLACE_WITH_ASIN":
        return f"https://www.amazon.fr/dp/ASIN?tag={AFFILIATE_TAG}"
    return f"https://www.amazon.fr/dp/{asin}?tag={AFFILIATE_TAG}"


def add_product_interactive():
    """Interactively add a new product"""
    print("\n=== Ajouter un produit Amazon ===\n")

    # Get console type
    print("Console:")
    print("1. Game Boy Color")
    print("2. Game Boy Advance")
    print("3. Général")
    console_choice = input("\nChoisissez (1-3): ").strip()

    console_map = {
        '1': 'game-boy-color',
        '2': 'game-boy-advance',
        '3': 'general'
    }

    console = console_map.get(console_choice, 'game-boy-color')

    # Get category
    print("\nCatégorie:")
    print("1. Console")
    print("2. Jeu")
    print("3. Accessoire")
    print("4. Bundle")
    print("5. Livre")
    cat_choice = input("\nChoisissez (1-5): ").strip()

    cat_map = {
        '1': 'consoles',
        '2': 'games',
        '3': 'accessories',
        '4': 'bundles',
        '5': 'books'
    }

    category = cat_map.get(cat_choice, 'games')

    # Get product details
    print("\n" + "="*50)
    print("Entrez les détails du produit:")
    print("="*50)

    name = input("\nNom du produit: ").strip()
    asin = input("ASIN (code produit Amazon, ex: B00005QD2R): ").strip()
    price = input("Prix (ex: 49.99): ").strip()
    description = input("Description courte: ").strip()
    priority = input("Priorité (1=plus important, ex: 1): ").strip()

    # Create product object
    product = {
        'name': name,
        'asin': asin,
        'price': price,
        'description': description,
        'category': category.rstrip('s'),  # Remove plural
        'priority': int(priority) if priority.isdigit() else 1
    }

    # Load existing products
    products = load_products()

    # Add to appropriate category
    if console not in products:
        products[console] = {}
    if category not in products[console]:
        products[console][category] = []

    products[console][category].append(product)

    # Save
    save_products(products)

    print("\n✅ Produit ajouté!")
    print(f"\nLien affilié: {generate_affiliate_link(asin)}")


def generate_html_for_console(console_key):
    """Generate HTML recommendations for a console"""
    products = load_products()

    if console_key not in products:
        print(f"❌ Console '{console_key}' non trouvée")
        return

    console_data = products[console_key]
    html_parts = []

    html_parts.append('<div class="amazon-section">')
    html_parts.append('    <h3>🎮 Produits recommandés sur Amazon</h3>')
    html_parts.append('    <div class="amazon-grid">')

    # Collect all products sorted by priority
    all_products = []
    for category, items in console_data.items():
        all_products.extend(items)

    # Sort by priority
    all_products.sort(key=lambda x: x.get('priority', 999))

    # Take top 6
    for product in all_products[:6]:
        asin = product.get('asin', 'REPLACE_WITH_ASIN')
        link = generate_affiliate_link(asin)

        html_parts.append('        <a href="{}" class="amazon-product" target="_blank" rel="nofollow noopener"'.format(link))
        html_parts.append('           onclick="trackAmazonClick(\'{}\')">'.format(product['name'].lower().replace(' ', '-')[:20]))
        html_parts.append('            <div class="amazon-title">{}</div>'.format(product['name']))
        html_parts.append('            <div class="amazon-price">{}€</div>'.format(product['price']))
        html_parts.append('            <span class="amazon-button">Voir sur Amazon</span>')
        html_parts.append('        </a>')

    html_parts.append('    </div>')
    html_parts.append('</div>')

    html = '\n'.join(html_parts)

    # Save to file
    output_file = f'amazon_recommendations_{console_key}.html'
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write(html)

    print(f"\n✅ HTML généré: {output_file}")
    print(f"\nCopiez ce code dans votre template:")
    print("="*60)
    print(html)
    print("="*60)


def list_all_products():
    """List all products in database"""
    products = load_products()

    print("\n=== Produits Amazon ===\n")

    total = 0
    for console, categories in products.items():
        print(f"\n📦 {console.upper()}")
        for category, items in categories.items():
            print(f"  └─ {category}: {len(items)} produits")
            for item in items:
                asin = item.get('asin', 'MISSING')
                status = "✅" if asin != "REPLACE_WITH_ASIN" else "⚠️"
                print(f"     {status} {item['name']} ({item['price']}€)")
                if asin != "REPLACE_WITH_ASIN":
                    print(f"        {generate_affiliate_link(asin)}")
                total += 1

    print(f"\n📊 Total: {total} produits")
    need_asin = sum(1 for c in products.values() for cat in c.values() for item in cat if item.get('asin') == 'REPLACE_WITH_ASIN')
    print(f"⚠️  {need_asin} produits sans ASIN (à compléter)")


def show_help():
    """Show usage instructions"""
    print("""
🛒 Amazon Product Manager - PrixRetro

Commandes:
    add         Ajouter un nouveau produit
    generate    Générer HTML pour une console
    list        Lister tous les produits
    help        Afficher cette aide

Exemples:
    python3 add_amazon_products.py add
    python3 add_amazon_products.py generate game-boy-color
    python3 add_amazon_products.py list

Comment trouver l'ASIN d'un produit Amazon:
    1. Allez sur Amazon.fr
    2. Trouvez le produit que vous voulez
    3. Regardez l'URL: amazon.fr/dp/B00005QD2R
       ^^^^^^^^
       C'est l'ASIN!
    4. Copiez juste: B00005QD2R

Votre tag affilié: prixretro-21
Format des liens: https://www.amazon.fr/dp/ASIN?tag=prixretro-21
""")


if __name__ == '__main__':
    if len(sys.argv) < 2:
        show_help()
        sys.exit(0)

    command = sys.argv[1].lower()

    if command == 'add':
        add_product_interactive()

    elif command == 'generate':
        if len(sys.argv) < 3:
            print("Usage: python3 add_amazon_products.py generate <console>")
            print("\nConsoles disponibles:")
            products = load_products()
            for console in products.keys():
                print(f"  - {console}")
        else:
            console = sys.argv[2]
            generate_html_for_console(console)

    elif command == 'list':
        list_all_products()

    elif command == 'help':
        show_help()

    else:
        print(f"❌ Commande inconnue: {command}")
        show_help()
