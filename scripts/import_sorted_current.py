#!/usr/bin/env python3
"""
Import sorted current listings to database
Marks old listings as sold, inserts new ones
"""
import os
import sys
import json
import mysql.connector
from dotenv import load_dotenv

load_dotenv()

def get_db_connection():
    """Connect to MySQL database"""
    db_port = os.getenv('DB_PORT', '35831')
    db_port = int(db_port) if db_port and db_port.strip() else 35831

    return mysql.connector.connect(
        host=os.getenv('DB_HOST') or 'ba2247864-001.eu.clouddb.ovh.net',
        port=db_port,
        user=os.getenv('DB_USERNAME') or 'prixretro_user',
        password=os.getenv('DB_PASSWORD'),
        database=os.getenv('DB_DATABASE') or 'prixretro',
        charset='utf8mb4',
        collation='utf8mb4_unicode_ci'
    )

def mark_old_as_sold():
    """Mark all current listings as sold before importing new ones"""
    conn = get_db_connection()
    cursor = conn.cursor()

    cursor.execute("UPDATE current_listings SET is_sold = 1, updated_at = NOW()")
    affected = cursor.rowcount
    conn.commit()

    cursor.close()
    conn.close()

    print(f"🗑️  Marked {affected} old listings as sold")
    return affected

def import_listings(items):
    """Import sorted listings to database"""
    conn = get_db_connection()
    cursor = conn.cursor()

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
    skipped = 0

    for item in items:
        # Validate variant_id
        if not item.get('variant_id'):
            print(f"  ⚠️  Skipping (no variant): {item['title'][:50]}")
            skipped += 1
            continue

        try:
            cursor.execute(query, (
                int(item['variant_id']),
                item['item_id'],
                item['title'][:255],
                item['price'],
                item['url'][:500]
            ))
            inserted += cursor.rowcount
            print(f"  ✅ {item['title'][:60]}... - {item['price']}€")

        except Exception as e:
            print(f"  ❌ Error: {e}")
            skipped += 1
            continue

    conn.commit()
    cursor.close()
    conn.close()

    return inserted, skipped

def main():
    """Main import function"""
    if len(sys.argv) < 2:
        print("Usage: python3 import_sorted_current.py sorted_current_listings.json")
        sys.exit(1)

    json_file = sys.argv[1]
    if not os.path.exists(json_file):
        print(f"❌ File not found: {json_file}")
        sys.exit(1)

    print("🚀 Import Sorted Current Listings")
    print("=" * 60)

    # Load sorted data
    with open(json_file, 'r', encoding='utf-8') as f:
        items = json.load(f)

    print(f"📋 Loaded {len(items)} items from {json_file}\n")

    # Mark old listings as sold
    mark_old_as_sold()

    # Import new listings
    print("\n💾 Importing listings...\n")
    inserted, skipped = import_listings(items)

    print("\n" + "=" * 60)
    print(f"✅ Import complete!")
    print(f"📊 Inserted/Updated: {inserted}")
    print(f"⚠️  Skipped: {skipped}")
    print(f"📈 Total processed: {len(items)}")

if __name__ == '__main__':
    main()
