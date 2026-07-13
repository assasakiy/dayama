<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First delete it if it exists to avoid duplicates
        DB::table('settings')->where('key', 'seo.custom_seo_enabled')->delete();
        
        DB::table('settings')->insert([
            'id' => Str::uuid()->toString(),
            'group' => 'general',
            'context' => 'global',
            'key' => 'seo.custom_seo_enabled',
            'value' => 'false',
            'type' => 'boolean',
            'is_env' => false,
            'is_locked' => false,
            'description' => 'Enable to use custom SEO metadata instead of automatically generating it from Site Identity.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Disable sitemap setting so we don't show it in UI
        DB::table('settings')->where('key', 'seo.sitemap_enabled')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'seo.custom_seo_enabled')->delete();
    }
};
