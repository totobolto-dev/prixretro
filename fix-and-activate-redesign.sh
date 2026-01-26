#!/bin/bash

echo "🎨 PrixRetro Redesign - Activation Script"
echo "========================================="
echo ""

# Stop Sail
echo "1️⃣  Stopping Sail..."
./vendor/bin/sail down

# Generate key
echo "2️⃣  Generating APP_KEY..."
php artisan key:generate --force

# Start Sail
echo "3️⃣  Starting Sail..."
./vendor/bin/sail up -d

# Wait for containers
echo "4️⃣  Waiting for containers to be ready..."
sleep 10

# Clear caches inside container
echo "5️⃣  Clearing caches..."
./vendor/bin/sail artisan config:clear
./vendor/bin/sail artisan view:clear
./vendor/bin/sail artisan cache:clear

# Activate new homepage
echo "6️⃣  Activating new component-based homepage..."
mv resources/views/home.blade.php resources/views/home-old-extends.blade.php 2>/dev/null
mv resources/views/home-component.blade.php.bak resources/views/home.blade.php 2>/dev/null

# Clear views again
./vendor/bin/sail artisan view:clear

echo ""
echo "✅ Done! Visit http://localhost:8000 to see the new design"
echo ""
echo "If you still see errors, try:"
echo "  ./vendor/bin/sail artisan key:generate --force"
echo "  ./vendor/bin/sail restart"
