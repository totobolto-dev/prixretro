#!/bin/bash
# Convert images to WebP and strip metadata
# Handles misnamed AVIF files

IMAGES_DIR="${1:-scraped_images}"
QUALITY="${2:-85}"

if [ ! -d "$IMAGES_DIR" ]; then
    echo "❌ Directory not found: $IMAGES_DIR"
    exit 1
fi

echo "=========================================="
echo "🔄 WebP Converter + Optimizer"
echo "=========================================="
echo "Directory: $IMAGES_DIR"
echo "Quality: $QUALITY"
echo ""

total=0
converted=0
copied=0
errors=0

OUTPUT_DIR="${IMAGES_DIR}_webp"
mkdir -p "$OUTPUT_DIR"

echo "🔍 Scanning for images..."
mapfile -t images < <(find "$IMAGES_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" -o -iname "*.gif" -o -iname "*.webp" \))

echo "📊 Found ${#images[@]} images"
echo ""

for image in "${images[@]}"; do
    ((total++))

    rel_path="${image#$IMAGES_DIR/}"
    dir_path=$(dirname "$rel_path")
    filename=$(basename "$image")
    filename_noext="${filename%.*}"

    mkdir -p "$OUTPUT_DIR/$dir_path"

    # Detect actual file type
    filetype=$(file -b --mime-type "$image")

    echo "[$total/${#images[@]}] 📸 $filename"
    echo "  Type: $filetype"

    case "$filetype" in
        image/avif)
            # AVIF is already better than WebP, just copy
            output_file="$OUTPUT_DIR/$dir_path/${filename_noext}.avif"
            cp "$image" "$output_file"
            echo "  ✓ Copied as AVIF (already optimized)"
            ((copied++))
            ;;

        image/webp)
            # Already WebP, copy as-is
            output_file="$OUTPUT_DIR/$dir_path/${filename_noext}.webp"
            cp "$image" "$output_file"
            echo "  ✓ Copied WebP"
            ((copied++))
            ;;

        image/png|image/jpeg|image/gif)
            # Convert to WebP
            output_file="$OUTPUT_DIR/$dir_path/${filename_noext}.webp"

            if command -v cwebp >/dev/null 2>&1; then
                if cwebp -q "$QUALITY" "$image" -o "$output_file" >/dev/null 2>&1; then
                    echo "  ✓ Converted to WebP"
                    ((converted++))
                else
                    echo "  ✗ Conversion failed, copying original"
                    cp "$image" "$output_file"
                    ((errors++))
                fi
            else
                echo "  ⚠ cwebp not installed, copying original"
                cp "$image" "$output_file"
                ((copied++))
            fi
            ;;

        *)
            echo "  ⚠ Unknown type, copying as-is"
            output_file="$OUTPUT_DIR/$dir_path/$filename"
            cp "$image" "$output_file"
            ((copied++))
            ;;
    esac

    original_size=$(du -h "$image" | cut -f1)
    new_size=$(du -h "$output_file" | cut -f1)
    echo "  📦 $original_size → $new_size"
    echo ""
done

echo "=========================================="
echo "✅ Complete!"
echo "=========================================="
echo "Processed: $total"
echo "Converted to WebP: $converted"
echo "Copied (already optimized): $copied"
echo "Errors: $errors"
echo ""
echo "Output: $OUTPUT_DIR"
echo ""
echo "🎯 Next: cp -r $OUTPUT_DIR/* public/storage/variants/"
