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
        Schema::table('variants', function (Blueprint $table) {
            // Drop old search_terms JSON column if exists
            if (Schema::hasColumn('variants', 'search_terms')) {
                $table->dropColumn('search_terms');
            }
            // Add new search_term string column
            $table->string('search_term')->nullable()->after('full_slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn('search_term');
            $table->json('search_terms')->nullable()->after('full_slug');
        });
    }
};
