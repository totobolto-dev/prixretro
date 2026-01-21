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
        Schema::table('listings', function (Blueprint $table) {
            // Rename 'condition' to 'item_condition' (occasion/neuf)
            $table->renameColumn('condition', 'item_condition');
        });

        Schema::table('listings', function (Blueprint $table) {
            // Add 'completeness' enum for loose/cib/sealed
            $table->enum('completeness', ['loose', 'cib', 'sealed'])->nullable()->after('item_condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('completeness');
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->renameColumn('item_condition', 'condition');
        });
    }
};
