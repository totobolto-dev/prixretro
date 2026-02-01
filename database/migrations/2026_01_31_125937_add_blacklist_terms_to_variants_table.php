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
        // If column exists as JSON, convert to string
        if (Schema::hasColumn('variants', 'blacklist_terms')) {
            // Get all variants with JSON blacklist_terms
            $variants = \DB::table('variants')->whereNotNull('blacklist_terms')->get();

            foreach ($variants as $variant) {
                $decoded = json_decode($variant->blacklist_terms, true);
                if (is_array($decoded) && count($decoded) > 0) {
                    // Convert array to comma-separated string
                    $commaSeparated = implode(', ', $decoded);
                    \DB::table('variants')->where('id', $variant->id)->update([
                        'blacklist_terms' => $commaSeparated
                    ]);
                } else {
                    // Set to null if empty
                    \DB::table('variants')->where('id', $variant->id)->update([
                        'blacklist_terms' => null
                    ]);
                }
            }

            // Change column type to TEXT (no length limit)
            \DB::statement('ALTER TABLE variants MODIFY blacklist_terms TEXT NULL');
        } else {
            // Add as TEXT column (no length limit)
            Schema::table('variants', function (Blueprint $table) {
                $table->text('blacklist_terms')->nullable()->after('search_term');
            });
        }
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
