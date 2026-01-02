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
            // Drop foreign key constraint
            $table->dropForeign(['variant_id']);

            // Make variant_id nullable
            $table->foreignId('variant_id')->nullable()->change();

            // Re-add foreign key constraint (with nullable)
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['variant_id']);

            // Make variant_id NOT nullable again
            $table->foreignId('variant_id')->nullable(false)->change();

            // Re-add foreign key constraint
            $table->foreign('variant_id')
                ->references('id')
                ->on('variants')
                ->onDelete('cascade');
        });
    }
};
