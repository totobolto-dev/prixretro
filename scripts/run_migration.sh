#!/bin/bash
# Run on production server to apply migration

php artisan migrate --force
php artisan optimize:clear

