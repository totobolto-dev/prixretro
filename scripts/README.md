# Image Scraping & Optimization Scripts

## 1. Scrape Images from Altar of Gaming

**Script**: `scrape_console_images.py`

### What It Scrapes
This script targets **console model/variant pages** like:
- Nintendo GBC Models (all color variations)
- PS2 Controllers (all colors)
- SEGA Dreamcast Models
- Nintendo 64 Controllers
- And 40+ more console variant pages

### Requirements
```bash
pip install requests beautifulsoup4
```

### Usage
```bash
cd scripts
python3 scrape_console_images.py
```

**Options**:
1. **Auto-discover** (RECOMMENDED) - Finds all 40+ model/variant pages automatically
2. **Single URL** - Scrape a specific variant page

**Output**: `scraped_images/[console-name]/` folders with high-quality images

### What Gets Scraped
The script automatically:
- ✅ Finds all "Models", "Controllers", "Color Variations" pages
- ✅ Downloads full-resolution images (removes `-300x169` size suffixes)
- ✅ Skips UI elements (logos, icons, buttons)
- ✅ Skips tiny images (<200px)
- ✅ Organizes by console folders
- ✅ Avoids duplicates

---

## 2. Convert to WebP + Strip Metadata

**Script**: `convert_to_webp.sh`

### Requirements
```bash
# Ubuntu/Debian
sudo apt install webp mat2

# macOS
brew install webp
pip3 install mat2
```

### Usage
```bash
# Default: quality 85
./convert_to_webp.sh scraped_images

# Custom quality (1-100, higher = better but larger)
./convert_to_webp.sh scraped_images 90
```

**Output**: `scraped_images_webp/` with optimized images

---

## 3. Full Workflow

```bash
# Step 1: Scrape images
python3 scrape_console_images.py

# Step 2: Convert to WebP and strip metadata
./convert_to_webp.sh scraped_images 85

# Step 3: Review results
ls -lh scraped_images_webp/

# Step 4: Copy to project (after review)
cp -r scraped_images_webp/* ../public/storage/variants/

# Step 5: Clean up
rm -rf scraped_images scraped_images_webp
```

---

## Tips

### Image Quality Settings
- **85** - Good balance (default)
- **90** - High quality, slightly larger files
- **75** - More compression, smaller files

### Filtering Images
The scraper automatically skips:
- Images smaller than 200x200px (logos, icons)
- URLs containing: logo, icon, avatar, button, banner

### Manual Filtering
After scraping, review `scraped_images/` and delete:
- Duplicate images
- Low-quality photos
- Non-console images (accessories, boxes if you don't want them)

Then run the WebP conversion on the cleaned-up folder.

---

## Troubleshooting

### "mat2 not found"
```bash
# Ubuntu/Debian
sudo apt install mat2

# macOS
pip3 install mat2

# Alternative: Skip metadata stripping (edit convert_to_webp.sh)
# Comment out the mat2 lines (lines with mat2 command)
```

### "cwebp not found"
```bash
# Ubuntu/Debian
sudo apt install webp

# macOS
brew install webp
```

### Python dependencies
```bash
pip3 install --upgrade requests beautifulsoup4
```

---

## Example Output

```
scraped_images/
├── game-boy-color/
│   ├── 001_atomic-purple.jpg
│   ├── 002_teal-variant.jpg
│   └── 003_pokemon-edition.jpg
├── playstation/
│   ├── 001_ps1-console.jpg
│   └── 002_gray-variant.jpg
└── ...

scraped_images_webp/
├── game-boy-color/
│   ├── 001_atomic-purple.webp  # Smaller, no metadata
│   ├── 002_teal-variant.webp
│   └── 003_pokemon-edition.webp
└── ...
```

Size savings typically: **60-80% reduction**
