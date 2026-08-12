<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('banner');
            $table->string('username', 60)->nullable()->change();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->string('banner')->nullable()->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name');
            $table->string('banner')->nullable();
            $table->string('username', 60)->nullable(false)->change();
        });

        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn('banner');
        });
    }
};
