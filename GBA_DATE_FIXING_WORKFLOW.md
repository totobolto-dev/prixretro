# GBA Date Fixing Workflow

## Overview

The GBA pages are live with correct pricing but placeholder dates (2025-12-25). This workflow lets you manually fix the dates by checking each item on eBay.

## Steps

### 1. Open the Date Editor

```bash
# Open in your browser
firefox gba_date_editor.html
# or
google-chrome gba_date_editor.html
```

### 2. Edit Dates & Delete Items

For each item (65 total):

1. **Click the date field** - Current date shows as `2025-12-25`
2. **Open eBay** - Click "Open eBay →" button or press Enter while in date field
3. **Find sold date** - Look for "Vendu le [date]" on eBay listing
4. **Enter correct date** - Type in format `YYYY-MM-DD` (e.g., `2024-12-15`)
5. **Auto-saves** - Changes save automatically to browser localStorage

**Delete items:**
- Click 🗑 (trash icon) to mark item for deletion
- Deleted items are grayed out with strikethrough
- Click ↺ (recycle icon) to undo deletion
- Deleted items are excluded from export

**Keyboard shortcut:** Edit date, press Enter to open eBay

**Visual feedback:**
- Edited items have blue border
- Deleted items are grayed out with red border
- Counter shows "Edited: X/65" and "Deleted: X"
- "✓ Auto-saved" indicator appears after each change

### 3. Export Fixed Dates

When done editing:

1. Click **"💾 Export JSON"** button
2. Saves as `gba_kept_items_fixed_dates.json`

### 4. Update Pages

Run the update script:

```bash
python3 update_gba_dates.py
```

This will:
- ✅ Update `gba_kept_items_with_dates.json` with your edits
- ✅ Regenerate `scraped_data_gba.json` with correct dates
- ✅ Regenerate all 19 GBA HTML pages
- ✅ Show summary of changes

### 5. Deploy

Commit and push the updated pages:

```bash
git add .
git commit -m "Fix GBA sold dates with actual eBay data"
git push origin main
```

GitHub Actions will automatically deploy to prixretro.com.

## Files

| File | Purpose |
|------|---------|
| `gba_date_editor.html` | Interactive date editing interface |
| `gba_kept_items_with_dates.json` | Source data (65 items) |
| `gba_kept_items_fixed_dates.json` | Exported after editing |
| `update_gba_dates.py` | Script to apply edits and regenerate pages |

## Tips

- **No need to do all 65 at once** - Editor auto-saves progress to browser localStorage
- **Export regularly** - Click "💾 Export JSON" to backup your work
- **Date format matters** - Must be `YYYY-MM-DD` (e.g., `2024-12-15`)
- **Check eBay carefully** - Some listings show "Vendu le 15 déc. 2024" or similar French format

## Troubleshooting

**Editor won't load?**
- Make sure `gba_date_editor.html` and `gba_kept_items_with_dates.json` are in the same directory
- Open browser console (F12) to see errors

**Lost your edits?**
- Edits are in browser localStorage (per-browser)
- Export JSON regularly to save externally

**Script fails?**
- Check `gba_kept_items_fixed_dates.json` exists
- Verify date format is correct (YYYY-MM-DD)
- Check `process_gba_for_launch.py` and `update_site_gba.py` exist
