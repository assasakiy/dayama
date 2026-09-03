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
        // Hanya drop index jika database driver bukan sqlite dan index memang ada
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropUnique('settings_key_unique');
                $table->unique(['key', 'context']);
            });
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
