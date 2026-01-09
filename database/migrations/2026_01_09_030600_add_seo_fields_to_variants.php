<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->text('description')->nullable()->after('slug');
            $table->string('release_date', 50)->nullable()->after('description');
            $table->string('region', 50)->nullable()->after('release_date');
            $table->string('rarity_level', 20)->nullable()->after('region');
        });
    }

    public function down(): void
    {
        Schema::table('variants', function (Blueprint $table) {
            $table->dropColumn(['description', 'release_date', 'region', 'rarity_level']);
        });
    }
};
