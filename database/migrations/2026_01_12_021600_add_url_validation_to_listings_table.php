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
            $table->enum('url_validation_status', ['pending', 'valid', 'invalid', 'captcha', 'error'])->nullable()->after('url');
            $table->string('url_redirect_target')->nullable()->after('url_validation_status');
            $table->text('url_validation_error')->nullable()->after('url_redirect_target');
            $table->timestamp('url_validated_at')->nullable()->after('url_validation_error');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'url_validation_status',
                'url_redirect_target',
                'url_validation_error',
                'url_validated_at'
            ]);
        });
    }
};
