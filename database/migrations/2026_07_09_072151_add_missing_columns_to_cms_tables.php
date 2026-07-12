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
        Schema::table('categories', function (Blueprint $table) {
            $table->uuid('deleted_by')->nullable()->after('updated_by');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->uuid('deleted_by')->nullable()->after('updated_by');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('deleted_by');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('deleted_by');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });
    }
};
