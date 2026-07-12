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
        Schema::rename('bank_ups', 'backups');

        Schema::table('backups', function (Blueprint $table) {
            $table->renameColumn('bankupable_type', 'backupable_type');
            $table->renameColumn('bankupable_id', 'backupable_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backups', function (Blueprint $table) {
            $table->renameColumn('backupable_type', 'bankupable_type');
            $table->renameColumn('backupable_id', 'bankupable_id');
        });

        Schema::rename('backups', 'bank_ups');
    }
};
