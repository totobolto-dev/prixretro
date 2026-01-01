<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('console_slug')->nullable()->after('variant_id')->index();
            $table->string('classification_status')->default('pending')->after('status')->index();
            // classification_status: unclassified, classified, approved, rejected
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['console_slug', 'classification_status']);
        });
    }
};
