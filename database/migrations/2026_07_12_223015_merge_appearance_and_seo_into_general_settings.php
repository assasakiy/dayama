<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Move all settings from appearance and seo groups to general
        DB::table('settings')
            ->whereIn('group', ['appearance', 'seo'])
            ->update(['group' => 'general']);

        // Delete the setting_groups records for appearance and seo if they exist
        DB::table('setting_groups')
            ->whereIn('key', ['appearance', 'seo'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We can't safely reverse this automatically without losing data on what came from where,
        // but we could recreate the groups at least if we wanted. 
        // For now, it's a one-way structural change in production.
    }
};
