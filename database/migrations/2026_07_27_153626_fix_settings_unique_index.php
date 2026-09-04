<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        $indexes = collect(DB::select('SHOW INDEXES FROM settings'))->pluck('Key_name')->all();

        Schema::table('settings', function (Blueprint $table) use ($indexes) {
            if (in_array('settings_key_unique', $indexes, true)) {
                $table->dropUnique('settings_key_unique');
            }

            if (! in_array('settings_key_context_unique', $indexes, true)) {
                $table->unique(['key', 'context']);
            }
        });
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
