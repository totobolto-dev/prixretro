<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $consoles = [
            // Nintendo Home Consoles (10-19)
            [
                'name' => 'NES',
                'slug' => 'nes',
                'manufacturer' => 'Nintendo',
                'release_year' => 1985,
                'short_name' => 'NES',
                'search_term' => 'nes nintendo entertainment system',
                'description' => 'La console 8-bit emblématique de Nintendo (1985)',
                'display_order' => 10,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'Super Nintendo',
                'slug' => 'super-nintendo',
                'manufacturer' => 'Nintendo',
                'release_year' => 1992,
                'short_name' => 'SNES',
                'search_term' => 'super nintendo snes',
                'description' => 'La console 16-bit de Nintendo (1992)',
                'display_order' => 11,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'Nintendo 64',
                'slug' => 'nintendo-64',
                'manufacturer' => 'Nintendo',
                'release_year' => 1997,
                'short_name' => 'N64',
                'search_term' => 'nintendo 64 n64',
                'description' => 'La première console 64-bit de Nintendo (1997)',
                'display_order' => 12,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'GameCube',
                'slug' => 'gamecube',
                'manufacturer' => 'Nintendo',
                'release_year' => 2002,
                'short_name' => 'GC',
                'search_term' => 'gamecube nintendo',
                'description' => 'Console de salon compacte de Nintendo (2002)',
                'display_order' => 13,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],

            // PlayStation Consoles (20-29)
            [
                'name' => 'PlayStation',
                'slug' => 'playstation',
                'manufacturer' => 'Sony',
                'release_year' => 1995,
                'short_name' => 'PS1',
                'search_term' => 'playstation ps1 psx',
                'description' => 'La première PlayStation de Sony (1995)',
                'display_order' => 20,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'PlayStation 2',
                'slug' => 'playstation-2',
                'manufacturer' => 'Sony',
                'release_year' => 2000,
                'short_name' => 'PS2',
                'search_term' => 'playstation 2 ps2',
                'description' => 'La console la plus vendue de tous les temps (2000)',
                'display_order' => 21,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],

            // Sega Consoles (30-39)
            [
                'name' => 'Sega Master System',
                'slug' => 'master-system',
                'manufacturer' => 'Sega',
                'release_year' => 1986,
                'short_name' => 'SMS',
                'search_term' => 'sega master system',
                'description' => 'Console 8-bit de Sega (1986)',
                'display_order' => 30,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'Sega Mega Drive',
                'slug' => 'mega-drive',
                'manufacturer' => 'Sega',
                'release_year' => 1990,
                'short_name' => 'MD',
                'search_term' => 'sega mega drive genesis',
                'description' => 'Console 16-bit de Sega (1990)',
                'display_order' => 31,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'Sega Saturn',
                'slug' => 'saturn',
                'manufacturer' => 'Sega',
                'release_year' => 1995,
                'short_name' => 'Saturn',
                'search_term' => 'sega saturn',
                'description' => 'Console 32-bit de Sega (1995)',
                'display_order' => 32,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
            [
                'name' => 'Sega Dreamcast',
                'slug' => 'dreamcast',
                'manufacturer' => 'Sega',
                'release_year' => 1999,
                'short_name' => 'DC',
                'search_term' => 'sega dreamcast',
                'description' => 'Dernière console de Sega (1999)',
                'display_order' => 33,
                'ebay_category_id' => 139971,
                'is_active' => true,
            ],
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
            'nes', 'super-nintendo', 'nintendo-64', 'gamecube',
            'playstation', 'playstation-2',
            'master-system', 'mega-drive', 'saturn', 'dreamcast'
        ];

        DB::table('consoles')->whereIn('slug', $slugs)->delete();
    }
};
