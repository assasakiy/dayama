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
        // Cegah crash di SQLite in-memory jika settings_key_unique tidak ada
        try {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_unique');
                $table->unique(['key', 'context']);
            });
        } catch (\Throwable $e) {
            // Index sudah ['key', 'context'] di create_settings_table
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key', 'context']);
            $table->unique('key');
        });
    }
};
