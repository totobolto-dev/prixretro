<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $consoles = [
            // Microsoft (40-49)
            ['name' => 'Xbox', 'slug' => 'xbox', 'manufacturer' => 'Microsoft', 'release_year' => 2001, 'short_name' => 'Xbox', 'search_term' => 'xbox microsoft', 'description' => 'Première console de Microsoft (2001)', 'display_order' => 40, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Xbox 360', 'slug' => 'xbox-360', 'manufacturer' => 'Microsoft', 'release_year' => 2005, 'short_name' => 'X360', 'search_term' => 'xbox 360', 'description' => 'Console 7ème génération de Microsoft (2005)', 'display_order' => 41, 'ebay_category_id' => 139971, 'is_active' => true],

            // Nintendo Wii (50-59)
            ['name' => 'Wii', 'slug' => 'wii', 'manufacturer' => 'Nintendo', 'release_year' => 2006, 'short_name' => 'Wii', 'search_term' => 'nintendo wii', 'description' => 'Console révolutionnaire avec détection de mouvement (2006)', 'display_order' => 50, 'ebay_category_id' => 139971, 'is_active' => true],

            // Sony PSP (60-69)
            ['name' => 'PSP', 'slug' => 'psp', 'manufacturer' => 'Sony', 'release_year' => 2004, 'short_name' => 'PSP', 'search_term' => 'psp playstation portable', 'description' => 'Console portable multimédia de Sony (2004)', 'display_order' => 60, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'PSP Go', 'slug' => 'psp-go', 'manufacturer' => 'Sony', 'release_year' => 2009, 'short_name' => 'PSP Go', 'search_term' => 'psp go playstation', 'description' => 'Version compacte coulissante de la PSP (2009)', 'display_order' => 61, 'ebay_category_id' => 139971, 'is_active' => true],

            // Sega Handhelds (70-79)
            ['name' => 'Game Gear', 'slug' => 'game-gear', 'manufacturer' => 'Sega', 'release_year' => 1990, 'short_name' => 'GG', 'search_term' => 'sega game gear', 'description' => 'Console portable couleur de Sega (1990)', 'display_order' => 70, 'ebay_category_id' => 139971, 'is_active' => true],

            // Atari (80-89)
            ['name' => 'Atari 2600', 'slug' => 'atari-2600', 'manufacturer' => 'Atari', 'release_year' => 1977, 'short_name' => '2600', 'search_term' => 'atari 2600', 'description' => 'Console emblématique de la 2ème génération (1977)', 'display_order' => 80, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Atari 5200', 'slug' => 'atari-5200', 'manufacturer' => 'Atari', 'release_year' => 1982, 'short_name' => '5200', 'search_term' => 'atari 5200', 'description' => 'Console 8-bit d\'Atari (1982)', 'display_order' => 81, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Atari 7800', 'slug' => 'atari-7800', 'manufacturer' => 'Atari', 'release_year' => 1986, 'short_name' => '7800', 'search_term' => 'atari 7800', 'description' => 'Console rétrocompatible 2600 (1986)', 'display_order' => 82, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Atari Lynx', 'slug' => 'atari-lynx', 'manufacturer' => 'Atari', 'release_year' => 1989, 'short_name' => 'Lynx', 'search_term' => 'atari lynx', 'description' => 'Première console portable couleur (1989)', 'display_order' => 83, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Atari Jaguar', 'slug' => 'atari-jaguar', 'manufacturer' => 'Atari', 'release_year' => 1993, 'short_name' => 'Jaguar', 'search_term' => 'atari jaguar', 'description' => 'Dernière console d\'Atari, 64-bit (1993)', 'display_order' => 84, 'ebay_category_id' => 139971, 'is_active' => true],

            // Neo Geo (90-95) - Note: Game & Watch already at 90
            ['name' => 'Neo Geo AES', 'slug' => 'neo-geo-aes', 'manufacturer' => 'SNK', 'release_year' => 1990, 'short_name' => 'AES', 'search_term' => 'neo geo aes', 'description' => 'Version salon du système arcade (1990)', 'display_order' => 91, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Neo Geo Pocket', 'slug' => 'neo-geo-pocket', 'manufacturer' => 'SNK', 'release_year' => 1998, 'short_name' => 'NGP', 'search_term' => 'neo geo pocket', 'description' => 'Console portable monochrome de SNK (1998)', 'display_order' => 92, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Neo Geo Pocket Color', 'slug' => 'neo-geo-pocket-color', 'manufacturer' => 'SNK', 'release_year' => 1999, 'short_name' => 'NGPC', 'search_term' => 'neo geo pocket color', 'description' => 'Version couleur de la Neo Geo Pocket (1999)', 'display_order' => 93, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Neo Geo CD', 'slug' => 'neo-geo-cd', 'manufacturer' => 'SNK', 'release_year' => 1994, 'short_name' => 'NGCD', 'search_term' => 'neo geo cd', 'description' => 'Version CD de la Neo Geo (1994)', 'display_order' => 94, 'ebay_category_id' => 139971, 'is_active' => true],

            // PC Engine / TurboGrafx (96-97)
            ['name' => 'PC Engine', 'slug' => 'pc-engine', 'manufacturer' => 'NEC', 'release_year' => 1987, 'short_name' => 'PCE', 'search_term' => 'pc engine turbografx', 'description' => 'Console 16-bit japonaise / TurboGrafx-16 (1987)', 'display_order' => 96, 'ebay_category_id' => 139971, 'is_active' => true],

            // WonderSwan (98-99)
            ['name' => 'WonderSwan', 'slug' => 'wonderswan', 'manufacturer' => 'Bandai', 'release_year' => 1999, 'short_name' => 'WS', 'search_term' => 'wonderswan bandai', 'description' => 'Console portable créée par Gunpei Yokoi (1999)', 'display_order' => 98, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'WonderSwan Color', 'slug' => 'wonderswan-color', 'manufacturer' => 'Bandai', 'release_year' => 2000, 'short_name' => 'WSC', 'search_term' => 'wonderswan color', 'description' => 'Version couleur de la WonderSwan (2000)', 'display_order' => 99, 'ebay_category_id' => 139971, 'is_active' => true],

            // Update existing Game Boy ranges to fit new structure (100-119)
            // Update existing DS ranges to fit new structure (120-139)
            // Update existing 3DS/2DS ranges to fit new structure (140-159)

            // Virtual Boy (160-169)
            ['name' => 'Virtual Boy', 'slug' => 'virtual-boy', 'manufacturer' => 'Nintendo', 'release_year' => 1995, 'short_name' => 'VB', 'search_term' => 'virtual boy nintendo', 'description' => 'Console 3D stéréoscopique de Nintendo (1995)', 'display_order' => 160, 'ebay_category_id' => 139971, 'is_active' => true],

            // 3DO (170-179)
            ['name' => '3DO', 'slug' => '3do', 'manufacturer' => 'Panasonic', 'release_year' => 1993, 'short_name' => '3DO', 'search_term' => '3do interactive multiplayer', 'description' => 'Console multimédia 32-bit (1993)', 'display_order' => 170, 'ebay_category_id' => 139971, 'is_active' => true],

            // Philips CD-i (180-189)
            ['name' => 'CD-i', 'slug' => 'cd-i', 'manufacturer' => 'Philips', 'release_year' => 1991, 'short_name' => 'CD-i', 'search_term' => 'philips cd-i cdi', 'description' => 'Lecteur multimédia interactif (1991)', 'display_order' => 180, 'ebay_category_id' => 139971, 'is_active' => true],

            // Classic Consoles (190-199)
            ['name' => 'Vectrex', 'slug' => 'vectrex', 'manufacturer' => 'GCE', 'release_year' => 1982, 'short_name' => 'Vectrex', 'search_term' => 'vectrex', 'description' => 'Console vectorielle avec écran intégré (1982)', 'display_order' => 190, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'ColecoVision', 'slug' => 'colecovision', 'manufacturer' => 'Coleco', 'release_year' => 1982, 'short_name' => 'Coleco', 'search_term' => 'colecovision coleco', 'description' => 'Console 8-bit de 2ème génération (1982)', 'display_order' => 191, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Intellivision', 'slug' => 'intellivision', 'manufacturer' => 'Mattel', 'release_year' => 1979, 'short_name' => 'Intv', 'search_term' => 'intellivision mattel', 'description' => 'Première console 16-bit (1979)', 'display_order' => 192, 'ebay_category_id' => 139971, 'is_active' => true],
            ['name' => 'Odyssey 2', 'slug' => 'odyssey-2', 'manufacturer' => 'Magnavox', 'release_year' => 1978, 'short_name' => 'O2', 'search_term' => 'odyssey 2 magnavox', 'description' => 'Console 8-bit de Magnavox (1978)', 'display_order' => 193, 'ebay_category_id' => 139971, 'is_active' => true],

            // Tiger (194)
            ['name' => 'Game.com', 'slug' => 'game-com', 'manufacturer' => 'Tiger', 'release_year' => 1997, 'short_name' => 'Game.com', 'search_term' => 'game.com tiger', 'description' => 'Console portable avec écran tactile (1997)', 'display_order' => 194, 'ebay_category_id' => 139971, 'is_active' => true],
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

        // Add to existing console families from previous migration
        // PlayStation family (20-29)
        DB::table('consoles')->insertOrIgnore([
            'name' => 'PlayStation 3',
            'slug' => 'playstation-3',
            'manufacturer' => 'Sony',
            'release_year' => 2006,
            'short_name' => 'PS3',
            'search_term' => 'playstation 3 ps3 sony',
            'description' => 'Console 7ème génération de Sony (2006)',
            'display_order' => 22,
            'ebay_category_id' => 139971,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Sega family (30-39)
        DB::table('consoles')->insertOrIgnore([
            'name' => 'Mega CD',
            'slug' => 'mega-cd',
            'manufacturer' => 'Sega',
            'release_year' => 1991,
            'short_name' => 'MCD',
            'search_term' => 'mega cd sega cd',
            'description' => 'Extension CD pour Mega Drive (1991)',
            'display_order' => 36,
            'ebay_category_id' => 139971,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('consoles')->insertOrIgnore([
            'name' => '32X',
            'slug' => '32x',
            'manufacturer' => 'Sega',
            'release_year' => 1994,
            'short_name' => '32X',
            'search_term' => 'sega 32x',
            'description' => 'Extension 32-bit pour Mega Drive (1994)',
            'display_order' => 37,
            'ebay_category_id' => 139971,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update existing console display_orders to new ranges
        DB::table('consoles')->where('slug', 'game-boy')->update(['display_order' => 100]);
        DB::table('consoles')->where('slug', 'game-boy-pocket')->update(['display_order' => 101]);
        DB::table('consoles')->where('slug', 'game-boy-light')->update(['display_order' => 102]);
        DB::table('consoles')->where('slug', 'game-boy-color')->update(['display_order' => 103]);
        DB::table('consoles')->where('slug', 'game-boy-advance')->update(['display_order' => 104]);
        DB::table('consoles')->where('slug', 'game-boy-advance-sp')->update(['display_order' => 105]);
        DB::table('consoles')->where('slug', 'game-boy-micro')->update(['display_order' => 106]);

        DB::table('consoles')->where('slug', 'nintendo-ds')->update(['display_order' => 120]);
        DB::table('consoles')->where('slug', 'nintendo-ds-lite')->update(['display_order' => 121]);
        DB::table('consoles')->where('slug', 'nintendo-dsi')->update(['display_order' => 122]);
        DB::table('consoles')->where('slug', 'nintendo-dsi-xl')->update(['display_order' => 123]);

        DB::table('consoles')->where('slug', 'nintendo-3ds')->update(['display_order' => 140]);
        DB::table('consoles')->where('slug', 'nintendo-3ds-xl')->update(['display_order' => 141]);
        DB::table('consoles')->where('slug', 'nintendo-2ds')->update(['display_order' => 142]);
        DB::table('consoles')->where('slug', 'new-nintendo-3ds')->update(['display_order' => 143]);
        DB::table('consoles')->where('slug', 'new-nintendo-3ds-xl')->update(['display_order' => 144]);
        DB::table('consoles')->where('slug', 'new-nintendo-2ds-xl')->update(['display_order' => 145]);
    }

    public function down(): void
    {
        $slugs = [
            'xbox', 'xbox-360', 'wii', 'psp', 'psp-go', 'game-gear',
            'atari-2600', 'atari-5200', 'atari-7800', 'atari-lynx', 'atari-jaguar',
            'neo-geo-aes', 'neo-geo-pocket', 'neo-geo-pocket-color', 'neo-geo-cd',
            'pc-engine', 'wonderswan', 'wonderswan-color',
            'virtual-boy', '3do', 'cd-i',
            'vectrex', 'colecovision', 'intellivision', 'odyssey-2', 'game-com',
            'playstation-3', 'mega-cd', '32x'
        ];

        DB::table('consoles')->whereIn('slug', $slugs)->delete();
    }
};
