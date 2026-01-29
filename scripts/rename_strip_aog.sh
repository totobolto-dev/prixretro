#!/bin/bash
# Strip "-altar-of-gaming" from all filenames in scraped_images

IMAGES_DIR="${1:-scraped_images}"

if [ ! -d "$IMAGES_DIR" ]; then
    echo "❌ Directory not found: $IMAGES_DIR"
    exit 1
fi

echo "🔄 Stripping '-altar-of-gaming' from filenames..."
echo ""

count=0
renamed=0

# Find all files recursively
find "$IMAGES_DIR" -type f | while read -r filepath; do
    ((count++))

    dir=$(dirname "$filepath")
    filename=$(basename "$filepath")

    # Check if filename contains the string to remove
    if [[ "$filename" == *"-altar-of-gaming"* ]]; then
        # Remove the string
        newname="${filename//-altar-of-gaming/}"
        newpath="$dir/$newname"

        echo "✓ $filename"
        echo "  → $newname"

        mv "$filepath" "$newpath"
        ((renamed++))
    fi
done

echo ""
echo "✅ Done! Renamed $renamed files"
