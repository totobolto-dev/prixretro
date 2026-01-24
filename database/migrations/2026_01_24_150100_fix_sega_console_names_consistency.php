<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all Sega console names to have consistent "Sega" prefix
        $updates = [
            'master-system-2' => [
                'name' => 'Sega Master System 2',
                'short_name' => 'SMS2',
            ],
            'genesis' => [
                'name' => 'Sega Genesis',
                'short_name' => 'Genesis',
            ],
            'mega-drive-ii' => [
                'name' => 'Sega Mega Drive 2',
                'short_name' => 'MD2',
            ],
            'mega-cd' => [
                'name' => 'Sega Mega CD',
                'short_name' => 'MCD',
            ],
            '32x' => [
                'name' => 'Sega 32X',
                'short_name' => '32X',
            ],
            'game-gear' => [
                'name' => 'Sega Game Gear',
                'short_name' => 'GG',
            ],
        ];

        foreach ($updates as $slug => $data) {
            DB::table('consoles')
                ->where('slug', $slug)
                ->update([
                    'name' => $data['name'],
                    'short_name' => $data['short_name'],
                    'updated_at' => now(),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original names
        $reverts = [
            'master-system-2' => [
                'name' => 'Master System 2',
                'short_name' => 'MS2',
            ],
            'genesis' => [
                'name' => 'Genesis',
                'short_name' => 'Genesis',
            ],
            'mega-drive-ii' => [
                'name' => 'Mega Drive II',
                'short_name' => 'MD2',
            ],
            'mega-cd' => [
                'name' => 'Mega CD',
                'short_name' => 'MCD',
            ],
            '32x' => [
                'name' => '32X',
                'short_name' => '32X',
            ],
            'game-gear' => [
                'name' => 'Game Gear',
                'short_name' => 'GG',
            ],
        ];

        foreach ($reverts as $slug => $data) {
            DB::table('consoles')
                ->where('slug', $slug)
                ->update([
                    'name' => $data['name'],
                    'short_name' => $data['short_name'],
                    'updated_at' => now(),
                ]);
        }
    }
};
