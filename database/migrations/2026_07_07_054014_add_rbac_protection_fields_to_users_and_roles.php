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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_primary_super_admin')->default(false)->after('id');
            $table->boolean('is_protected')->default(false)->after('is_primary_super_admin');
            $table->boolean('is_verified')->default(false)->after('is_protected');
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedInteger('rank')->default(10)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_primary_super_admin', 'is_protected', 'is_verified']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('rank');
        });
    }
};
