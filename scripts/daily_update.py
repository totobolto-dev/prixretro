#!/usr/bin/env python3
"""
Daily Update Workflow
====================
Automates: scraping → merge sorted data → regenerate site → commit → push

Usage:
    python scripts/daily_update.py          # Full workflow
    python scripts/daily_update.py --no-push   # Don't push to GitHub
"""

import subprocess
import sys
import os
from datetime import datetime

def run_command(cmd, description):
    """Run a shell command and print output"""
    print(f"\n{'='*60}")
    print(f"🔨 {description}")
    print(f"{'='*60}")

    result = subprocess.run(cmd, shell=True, capture_output=True, text=True)

    if result.stdout:
        print(result.stdout)
    if result.stderr:
        print(result.stderr, file=sys.stderr)

    if result.returncode != 0:
        print(f"❌ Error: {description} failed!")
        sys.exit(1)

    print(f"✅ {description} completed")
    return result


def main():
    """Run complete daily update workflow"""

    no_push = '--no-push' in sys.argv

    print("\n" + "="*60)
    print("🚀 PRIXRETRO DAILY UPDATE WORKFLOW")
    print("="*60)

    # Step 1: Merge sorted data (if exists)
    if os.path.exists('sorted_items.json') or any(f.startswith('sorted_items_') for f in os.listdir('.')):
        run_command(
            'python3 merge_sorted_data.py',
            'Step 1: Merge sorted data into scraped_data.json'
        )
    else:
        print("\n⚠️  No sorted data found, skipping merge")

    # Step 2: Scrape current listings with images
    run_command(
        'python3 scraper_current_listings.py',
        'Step 2: Scrape current eBay listings with images'
    )

    # Step 3: Regenerate website
    run_command(
        'python3 update_site_compact.py',
        'Step 3: Regenerate website'
    )

    # Step 4: Git add
    run_command(
        'git add scraped_data.json current_listings.json output/ styles.css',
        'Step 4: Stage files for commit'
    )

    # Step 5: Git commit
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M')
    commit_message = f"""Daily update: {timestamp}

Automated update with latest eBay data
- Updated current listings with images
- Regenerated all variant pages
- Updated price history

🤖 Generated with [Claude Code](https://claude.com/claude-code)

Co-Authored-By: Claude Sonnet 4.5 <noreply@anthropic.com>"""

    # Write commit message to temp file
    with open('/tmp/prixretro_commit_msg.txt', 'w') as f:
        f.write(commit_message)

    run_command(
        'git commit -F /tmp/prixretro_commit_msg.txt',
        'Step 5: Create commit'
    )

    # Step 6: Git push (if not disabled)
    if not no_push:
        run_command(
            'git push origin main',
            'Step 6: Push to GitHub'
        )
        print("\n🎉 Daily update complete! GitHub Actions will deploy to OVH.")
    else:
        print("\n✅ Daily update complete (not pushed to GitHub)")

    print("\n" + "="*60)
    print("✨ All done!")
    print("="*60 + "\n")


if __name__ == '__main__':
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n⚠️  Workflow cancelled by user")
        sys.exit(1)
    except Exception as e:
        print(f"\n\n❌ Unexpected error: {e}")
        sys.exit(1)
