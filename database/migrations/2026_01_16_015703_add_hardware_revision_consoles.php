<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $consoles = [
            // Nintendo hardware revisions (10-19)
            ['name' => 'Super Famicom', 'slug' => 'super-famicom', 'manufacturer' => 'Nintendo', 'release_year' => 1990, 'short_name' => 'SFC', 'search_term' => 'super famicom', 'description' => 'Version japonaise de la Super Nintendo (1990)', 'display_order' => 13, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'NES Top Loader', 'slug' => 'nes-top-loader', 'manufacturer' => 'Nintendo', 'release_year' => 1993, 'short_name' => 'NES-101', 'search_term' => 'nes top loader nes-101', 'description' => 'Version redessinée de la NES (1993)', 'display_order' => 11, 'ebay_category_id' => 139971, 'is_active' => true],

            // PlayStation hardware revisions (20-29)
            ['name' => 'PS One', 'slug' => 'ps-one', 'manufacturer' => 'Sony', 'release_year' => 2000, 'short_name' => 'PSone', 'search_term' => 'ps one psone playstation compact', 'description' => 'Version compacte de la PlayStation (2000)', 'display_order' => 21, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'PS2 Slim', 'slug' => 'ps2-slim', 'manufacturer' => 'Sony', 'release_year' => 2004, 'short_name' => 'PS2 Slim', 'search_term' => 'playstation 2 slim ps2 scph-7000x scph-9000x', 'description' => 'Version compacte de la PS2 (2004)', 'display_order' => 23, 'ebay_category_id' => 139971, 'is_active' => true],

            // Sega hardware revisions (30-39)
            ['name' => 'Mega Drive II', 'slug' => 'mega-drive-ii', 'manufacturer' => 'Sega', 'release_year' => 1993, 'short_name' => 'MD2', 'search_term' => 'mega drive 2 genesis 2', 'description' => 'Version redessinée de la Mega Drive (1993)', 'display_order' => 33, 'ebay_category_id' => 139971, 'is_active' => true],
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
            'super-famicom', 'nes-top-loader',
            'ps-one', 'ps2-slim',
            'mega-drive-ii'
        ];

        DB::table('consoles')->whereIn('slug', $slugs)->delete();
    }
};
