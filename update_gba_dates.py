#!/usr/bin/env python3
"""
Update GBA dates from manually edited data and regenerate pages
"""

import json
import subprocess

def update_dates():
    """Update scraped_data_gba.json with corrected dates"""

    print("📋 Loading manually edited dates...")

    # Load the manually edited data
    try:
        with open('gba_kept_items_fixed_dates.json', 'r', encoding='utf-8') as f:
            edited_data = json.load(f)
    except FileNotFoundError:
        print("❌ Error: gba_kept_items_fixed_dates.json not found!")
        print("   Export it from the HTML date editor first.")
        return False

    edited_items = edited_data['items']
    edited_count = edited_data.get('edited_count', 0)

    print(f"   ✅ Loaded {len(edited_items)} items ({edited_count} dates edited)")

    # Update gba_kept_items_with_dates.json
    print("\n📝 Updating gba_kept_items_with_dates.json...")
    with open('gba_kept_items_with_dates.json', 'w', encoding='utf-8') as f:
        json.dump({'items': edited_items}, f, indent=2, ensure_ascii=False)
    print("   ✅ Updated source file")

    # Regenerate scraped_data_gba.json with new dates
    print("\n🔄 Regenerating scraped_data_gba.json...")
    try:
        result = subprocess.run(
            ['python3', 'process_gba_for_launch.py'],
            capture_output=True,
            text=True,
            timeout=30
        )

        if result.returncode == 0:
            print("   ✅ Successfully regenerated scraped_data_gba.json")
        else:
            print(f"   ⚠️  Warning: process_gba_for_launch.py had issues:")
            print(result.stderr)
    except Exception as e:
        print(f"   ❌ Error running process_gba_for_launch.py: {e}")
        return False

    # Regenerate HTML pages
    print("\n🌐 Regenerating GBA HTML pages...")
    try:
        result = subprocess.run(
            ['python3', 'update_site_gba.py'],
            capture_output=True,
            text=True,
            timeout=30
        )

        if result.returncode == 0:
            print("   ✅ Successfully regenerated all GBA pages")
            print(result.stdout)
        else:
            print(f"   ❌ Error regenerating pages:")
            print(result.stderr)
            return False
    except Exception as e:
        print(f"   ❌ Error running update_site_gba.py: {e}")
        return False

    print("\n" + "="*60)
    print("✅ ALL DONE!")
    print("="*60)
    print(f"\n📊 Summary:")
    print(f"   - {len(edited_items)} items processed")
    print(f"   - {edited_count} dates manually corrected")
    print(f"   - 19 GBA variant pages regenerated")
    print(f"\n📌 Next steps:")
    print(f"   1. Review the updated pages in output/")
    print(f"   2. Commit changes: git add . && git commit -m 'Fix GBA dates'")
    print(f"   3. Push to deploy: git push origin main")

    return True

if __name__ == '__main__':
    success = update_dates()
    exit(0 if success else 1)
