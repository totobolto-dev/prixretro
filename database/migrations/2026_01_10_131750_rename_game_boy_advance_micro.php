<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('consoles')->where('slug', 'game-boy-advance-micro')->update([
            'slug' => 'game-boy-micro',
            'name' => 'Game Boy Micro',
            'short_name' => 'GB Micro',
            'updated_at' => now()
        ]);
    }

    public function down(): void
    {
        DB::table('consoles')->where('slug', 'game-boy-micro')->update([
            'slug' => 'game-boy-advance-micro',
            'name' => 'Game Boy Advance Micro',
            'short_name' => 'GBA Micro',
            'updated_at' => now()
        ]);
    }
};
