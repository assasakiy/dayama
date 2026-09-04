<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = Schema::hasTable('core_users') ? 'core_role_user' : 'role_user';
        $userTable = Schema::hasTable('core_users') ? 'core_users' : 'users';
        $roleTable = Schema::hasTable('core_roles') ? 'core_roles' : 'roles';
        $instTable = Schema::hasTable('core_institutions') ? 'core_institutions' : 'institutions';

        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table) use ($userTable, $roleTable, $instTable) {
                $table->uuid('id')->primary();
                $table->uuid('user_id');
                $table->uuid('role_id');
                $table->uuid('institution_id')->nullable();
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on($userTable)->cascadeOnDelete();
                $table->foreign('role_id')->references('id')->on($roleTable)->cascadeOnDelete();
                $table->foreign('institution_id')->references('id')->on($instTable)->nullOnDelete();

                $table->unique(['user_id', 'role_id', 'institution_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_role_user');
        Schema::dropIfExists('role_user');
    }
};
