#!/usr/bin/env python3
"""
Migrate scraped_data.json from flat structure to multi-console structure

OLD FORMAT:
{
  "variant-key": {
    "variant_key": "...",
    "variant_name": "...",
    "stats": {...},
    "listings": [...]
  }
}

NEW FORMAT:
{
  "consoles": {
    "game-boy-color": {
      "variants": {
        "variant-key": {
          "variant_key": "...",
          "variant_name": "...",
          "stats": {...},
          "listings": [...]
        }
      }
    },
    "game-boy-advance": {
      "variants": {...}
    }
  }
}
"""

import json
import os
from datetime import datetime

def migrate_scraped_data():
    """Migrate scraped_data.json to multi-console format"""

    print("="*70)
    print("🔄 MIGRATING TO MULTI-CONSOLE FORMAT")
    print("="*70)
    print()

    # Load current scraped_data.json
    if not os.path.exists('scraped_data.json'):
        print("❌ scraped_data.json not found!")
        return False

    with open('scraped_data.json', 'r', encoding='utf-8') as f:
        old_data = json.load(f)

    print(f"📂 Loaded {len(old_data)} variants from scraped_data.json")
    print()

    # Create new multi-console structure
    new_data = {
        "consoles": {
            "game-boy-color": {
                "variants": {}
            }
        },
        "metadata": {
            "migration_date": datetime.now().isoformat(),
            "version": "2.0",
            "format": "multi-console"
        }
    }

    # Move all existing variants to game-boy-color
    for variant_key, variant_data in old_data.items():
        new_data["consoles"]["game-boy-color"]["variants"][variant_key] = variant_data
        print(f"  ✅ Migrated: {variant_key} ({variant_data.get('variant_name', 'N/A')})")

    # Backup old file
    backup_file = 'scraped_data_flat_backup.json'
    with open(backup_file, 'w', encoding='utf-8') as f:
        json.dump(old_data, f, indent=2, ensure_ascii=False)
    print()
    print(f"💾 Backed up old format to: {backup_file}")

    # Write new format
    with open('scraped_data_multiconsole.json', 'w', encoding='utf-8') as f:
        json.dump(new_data, f, indent=2, ensure_ascii=False)

    print()
    print("="*70)
    print("✅ MIGRATION COMPLETE")
    print("="*70)
    print()
    print("📊 Summary:")
    print(f"   • Variants migrated: {len(old_data)}")
    print(f"   • Console categories: 1 (game-boy-color)")
    print()
    print("📁 Files created:")
    print(f"   • scraped_data_multiconsole.json (new format)")
    print(f"   • scraped_data_flat_backup.json (backup)")
    print()
    print("⚠️  IMPORTANT:")
    print("   The original scraped_data.json was NOT modified.")
    print("   To activate multi-console format:")
    print("   1. Review scraped_data_multiconsole.json")
    print("   2. When ready: mv scraped_data.json scraped_data_old.json")
    print("   3. Then: mv scraped_data_multiconsole.json scraped_data.json")
    print()

    return True

def migrate_current_listings():
    """Migrate current_listings.json to multi-console format"""

    if not os.path.exists('current_listings.json'):
        print("⚠️  current_listings.json not found, skipping")
        return True

    print("="*70)
    print("🔄 MIGRATING CURRENT LISTINGS")
    print("="*70)
    print()

    with open('current_listings.json', 'r', encoding='utf-8') as f:
        old_data = json.load(f)

    print(f"📂 Loaded {len(old_data)} variants from current_listings.json")
    print()

    # Create new multi-console structure
    new_data = {
        "consoles": {
            "game-boy-color": {
                "variants": {}
            }
        },
        "metadata": {
            "migration_date": datetime.now().isoformat(),
            "version": "2.0",
            "format": "multi-console"
        }
    }

    # Move all existing variants to game-boy-color
    for variant_key, variant_data in old_data.items():
        new_data["consoles"]["game-boy-color"]["variants"][variant_key] = variant_data
        count = len(variant_data.get('listings', []))
        print(f"  ✅ Migrated: {variant_key} ({count} listings)")

    # Backup old file
    backup_file = 'current_listings_flat_backup.json'
    with open(backup_file, 'w', encoding='utf-8') as f:
        json.dump(old_data, f, indent=2, ensure_ascii=False)
    print()
    print(f"💾 Backed up old format to: {backup_file}")

    # Write new format
    with open('current_listings_multiconsole.json', 'w', encoding='utf-8') as f:
        json.dump(new_data, f, indent=2, ensure_ascii=False)

    print()
    print("="*70)
    print("✅ MIGRATION COMPLETE")
    print("="*70)
    print()
    print("📁 Files created:")
    print(f"   • current_listings_multiconsole.json (new format)")
    print(f"   • current_listings_flat_backup.json (backup)")
    print()

    return True

if __name__ == '__main__':
    print()
    success1 = migrate_scraped_data()
    print()
    print()
    success2 = migrate_current_listings()

    if success1 and success2:
        print()
        print("🎉 All migrations complete!")
        print()
        print("📋 Next steps:")
        print("   1. Review the new *_multiconsole.json files")
        print("   2. Test the new format with update_site_compact.py")
        print("   3. When ready, rename files to activate:")
        print("      mv scraped_data.json scraped_data_old.json")
        print("      mv scraped_data_multiconsole.json scraped_data.json")
        print("      mv current_listings.json current_listings_old.json")
        print("      mv current_listings_multiconsole.json current_listings.json")
        print()
