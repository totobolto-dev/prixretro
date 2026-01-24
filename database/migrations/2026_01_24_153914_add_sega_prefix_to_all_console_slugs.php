<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add "sega-" prefix to all Sega console slugs for consistency with display names.
     * All Sega console names already have "Sega" prefix (fixed in previous migration),
     * but slugs were inconsistent.
     */
    public function up(): void
    {
        $slugUpdates = [
            'master-system' => 'sega-master-system',
            'master-system-2' => 'sega-master-system-2',
            'mega-drive' => 'sega-mega-drive',
            'mega-drive-ii' => 'sega-mega-drive-2', // Also fix roman numeral
            'genesis' => 'sega-genesis',
            'mega-cd' => 'sega-mega-cd',
            '32x' => 'sega-32x',
            'saturn' => 'sega-saturn',
            'dreamcast' => 'sega-dreamcast',
            'game-gear' => 'sega-game-gear',
        ];

        foreach ($slugUpdates as $oldSlug => $newSlug) {
            $console = DB::table('consoles')
                ->where('slug', $oldSlug)
                ->first();

            if (!$console) {
                continue; // Console doesn't exist, skip
            }

            // Update console slug
            DB::table('consoles')
                ->where('slug', $oldSlug)
                ->update([
                    'slug' => $newSlug,
                    'updated_at' => now(),
                ]);

            // Update all variants' full_slug that reference this console
            DB::table('variants')
                ->where('console_id', $console->id)
                ->update([
                    'full_slug' => DB::raw("REPLACE(full_slug, '$oldSlug/', '$newSlug/')"),
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $slugReverts = [
            'sega-master-system' => 'master-system',
            'sega-master-system-2' => 'master-system-2',
            'sega-mega-drive' => 'mega-drive',
            'sega-mega-drive-2' => 'mega-drive-ii',
            'sega-genesis' => 'genesis',
            'sega-mega-cd' => 'mega-cd',
            'sega-32x' => '32x',
            'sega-saturn' => 'saturn',
            'sega-dreamcast' => 'dreamcast',
            'sega-game-gear' => 'game-gear',
        ];

        foreach ($slugReverts as $oldSlug => $newSlug) {
            $console = DB::table('consoles')
                ->where('slug', $oldSlug)
                ->first();

            if (!$console) {
                continue;
            }

            // Revert console slug
            DB::table('consoles')
                ->where('slug', $oldSlug)
                ->update([
                    'slug' => $newSlug,
                    'updated_at' => now(),
                ]);

            // Revert variants' full_slug
            DB::table('variants')
                ->where('console_id', $console->id)
                ->update([
                    'full_slug' => DB::raw("REPLACE(full_slug, '$oldSlug/', '$newSlug/')"),
                    'updated_at' => now(),
                ]);
        }
    }
};
