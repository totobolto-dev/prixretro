<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change search_term and blacklist_terms to TEXT to avoid 255 char limit
        \DB::statement('ALTER TABLE variants MODIFY search_term TEXT NULL');
        \DB::statement('ALTER TABLE variants MODIFY blacklist_terms TEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \DB::statement('ALTER TABLE variants MODIFY search_term VARCHAR(255) NULL');
        \DB::statement('ALTER TABLE variants MODIFY blacklist_terms VARCHAR(255) NULL');
    }
};
