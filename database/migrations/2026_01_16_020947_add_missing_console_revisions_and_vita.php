<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $consoles = [
            // PlayStation family (20-29)
            ['name' => 'PS3 Slim', 'slug' => 'ps3-slim', 'manufacturer' => 'Sony', 'release_year' => 2009, 'short_name' => 'PS3 Slim', 'search_term' => 'playstation 3 slim ps3 cech-2000 cech-3000', 'description' => 'Version compacte de la PS3 (2009)', 'display_order' => 24, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'PS3 Super Slim', 'slug' => 'ps3-super-slim', 'manufacturer' => 'Sony', 'release_year' => 2012, 'short_name' => 'PS3 SS', 'search_term' => 'playstation 3 super slim ps3 cech-4000', 'description' => 'Version ultra-compacte de la PS3 (2012)', 'display_order' => 25, 'ebay_category_id' => 139971, 'is_active' => true],

            // Sega family (30-39)
            ['name' => 'Master System 2', 'slug' => 'master-system-2', 'manufacturer' => 'Sega', 'release_year' => 1990, 'short_name' => 'MS2', 'search_term' => 'master system 2 ii sega', 'description' => 'Version redessinée de la Master System (1990)', 'display_order' => 31, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Genesis', 'slug' => 'genesis', 'manufacturer' => 'Sega', 'release_year' => 1989, 'short_name' => 'Genesis', 'search_term' => 'sega genesis', 'description' => 'Version américaine de la Mega Drive (1989)', 'display_order' => 32, 'ebay_category_id' => 139971, 'is_active' => true],

            // Wii family (50-59)
            ['name' => 'Wii Mini', 'slug' => 'wii-mini', 'manufacturer' => 'Nintendo', 'release_year' => 2012, 'short_name' => 'Wii Mini', 'search_term' => 'wii mini nintendo', 'description' => 'Version compacte sans connexion internet (2012)', 'display_order' => 51, 'ebay_category_id' => 139971, 'is_active' => true],

            // Sony Vita (60-69)
            ['name' => 'PS Vita', 'slug' => 'ps-vita', 'manufacturer' => 'Sony', 'release_year' => 2011, 'short_name' => 'Vita', 'search_term' => 'playstation vita ps vita psvita', 'description' => 'Console portable haute définition de Sony (2011)', 'display_order' => 62, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'PS Vita Slim', 'slug' => 'ps-vita-slim', 'manufacturer' => 'Sony', 'release_year' => 2013, 'short_name' => 'Vita Slim', 'search_term' => 'playstation vita slim 2000', 'description' => 'Version allégée de la PS Vita (2013)', 'display_order' => 63, 'ebay_category_id' => 139971, 'is_active' => true],
        ];

        foreach ($consoles as $console) {
            DB::table('consoles')->insertOrIgnore([
                'name' => $console['name'],
                'slug' => $console['slug'],
                'manufacturer' => $console['manufacturer'],
                'release_year' => $console['release_year'],
                'short_name' => $console['short_name'],
                'search_term' => $console['search_term'],
                'description' => $console['description'],
                'display_order' => $console['display_order'],
                'ebay_category_id' => $console['ebay_category_id'],
                'is_active' => $console['is_active'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        $slugs = [
            'ps3-slim', 'ps3-super-slim',
            'master-system-2', 'genesis',
            'wii-mini',
            'ps-vita', 'ps-vita-slim'
        ];

        DB::table('consoles')->whereIn('slug', $slugs)->delete();
    }
};
