#!/bin/bash
# Organize scraped images into public/storage/variants/
# Renames with console prefix for clarity

SRC_DIR="${1:-scraped_images}"
DEST_DIR="public/storage/variants"

if [ ! -d "$SRC_DIR" ]; then
    echo "❌ Source directory not found: $SRC_DIR"
    exit 1
fi

mkdir -p "$DEST_DIR"

echo "📁 Organizing images into $DEST_DIR"
echo ""

total=0

# Process each console folder
for console_dir in "$SRC_DIR"/*; do
    if [ ! -d "$console_dir" ]; then
        continue
    fi

    console_name=$(basename "$console_dir")
    echo "📦 Processing: $console_name"

    # Copy all images from this console folder
    find "$console_dir" -type f \( -name "*.png" -o -name "*.jpg" -o -name "*.webp" -o -name "*.avif" \) | while read -r img; do
        filename=$(basename "$img")

        # Remove number prefix (001_, 002_, etc.)
        clean_name="${filename#[0-9][0-9][0-9]_}"

        # Add console prefix
        new_name="${console_name}_${clean_name}"

        cp "$img" "$DEST_DIR/$new_name"
        echo "  ✓ $new_name"

        ((total++))
    done

    echo ""
done

echo "✅ Copied $total images to $DEST_DIR"
echo ""
echo "🎯 Next: Run the matching script to assign images to variants"
