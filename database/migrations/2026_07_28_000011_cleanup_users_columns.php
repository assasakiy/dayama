<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $usersTable = Schema::hasTable('core_users') ? 'core_users' : 'users';
        $profilesTable = Schema::hasTable('core_user_profiles') ? 'core_user_profiles' : 'user_profiles';

        Schema::table($usersTable, function (Blueprint $table) use ($usersTable) {
            if (Schema::hasColumn($usersTable, 'name')) {
                $table->dropColumn('name');
            }
            if (Schema::hasColumn($usersTable, 'banner')) {
                $table->dropColumn('banner');
            }
            $table->string('username', 60)->nullable()->change();
        });

        if (Schema::hasTable($profilesTable) && ! Schema::hasColumn($profilesTable, 'banner')) {
            Schema::table($profilesTable, function (Blueprint $table) {
                $table->string('banner')->nullable()->after('avatar');
            });
        }
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
