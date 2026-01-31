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
            // Drop JSON column if exists
            if (Schema::hasColumn('variants', 'blacklist_terms')) {
                $table->dropColumn('blacklist_terms');
            }
        });

        Schema::table('variants', function (Blueprint $table) {
            // Add as string column
            $table->string('blacklist_terms')->nullable()->after('search_term');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn('blacklist_terms');
        });
    }
};
