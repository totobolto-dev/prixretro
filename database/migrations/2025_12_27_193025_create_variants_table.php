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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('console_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('full_slug')->unique();
            $table->json('search_terms')->nullable();
            $table->string('image_filename')->nullable();
            $table->string('rarity_level')->nullable();
            $table->string('region')->nullable();
            $table->boolean('is_special_edition')->default(false);
            $table->timestamps();

            $table->unique(['console_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
