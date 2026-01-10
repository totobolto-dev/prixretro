<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Shift existing Game Boy family consoles to make room for Pocket and Light
        DB::table('consoles')->where('slug', 'game-boy-color')->update(['display_order' => 105]);
        DB::table('consoles')->where('slug', 'game-boy-advance')->update(['display_order' => 106]);
        DB::table('consoles')->where('slug', 'game-boy-advance-sp')->update(['display_order' => 107]);
        DB::table('consoles')->where('slug', 'game-boy-advance-micro')->update(['display_order' => 108]);

        // Insert new consoles
        DB::table('consoles')->insert([
            [
                'slug' => 'game-watch',
                'name' => 'Game & Watch',
                'short_name' => 'G&W',
                'search_term' => 'game & watch',
                'ebay_category_id' => '139971',
                'description' => 'Les premières consoles portables de Nintendo (1980-1991)',
                'manufacturer' => 'Nintendo',
                'release_year' => 1980,
                'display_order' => 90,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'game-boy-pocket',
                'name' => 'Game Boy Pocket',
                'short_name' => 'GB Pocket',
                'search_term' => 'game boy pocket',
                'ebay_category_id' => '139971',
                'description' => 'Version compacte du Game Boy original, sortie en 1996',
                'manufacturer' => 'Nintendo',
                'release_year' => 1996,
                'display_order' => 101,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'slug' => 'game-boy-light',
                'name' => 'Game Boy Light',
                'short_name' => 'GB Light',
                'search_term' => 'game boy light',
                'ebay_category_id' => '139971',
                'description' => 'Game Boy rétroéclairé exclusif au Japon (1998)',
                'manufacturer' => 'Nintendo',
                'release_year' => 1998,
                'display_order' => 102,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        // Remove the new consoles
        DB::table('consoles')->whereIn('slug', [
            'game-watch',
            'game-boy-pocket',
            'game-boy-light',
        ])->delete();

        // Restore original display_order
        DB::table('consoles')->where('slug', 'game-boy-color')->update(['display_order' => 101]);
        DB::table('consoles')->where('slug', 'game-boy-advance')->update(['display_order' => 102]);
        DB::table('consoles')->where('slug', 'game-boy-advance-sp')->update(['display_order' => 103]);
        DB::table('consoles')->where('slug', 'game-boy-advance-micro')->update(['display_order' => 104]);
    }
};
