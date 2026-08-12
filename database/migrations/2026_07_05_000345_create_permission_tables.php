<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Override the default Spatie permission migration to support UUID
     * primary keys on models.
     */
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');
        $pivotRole = $columnNames['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), \Exception::class, 'Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');

        Schema::create($tableNames['permissions'], static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('module', 60)->nullable();
            $table->string('action', 60)->nullable();
            $table->string('scope', 30)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], static function (Blueprint $table) use ($teams, $columnNames): void {
            $table->uuid('id')->primary();
            if ($teams || config('permission.testing')) {
                $table->uuid($columnNames['team_foreign_key'])->nullable();
                $table->index($columnNames['team_foreign_key'], 'roles_team_foreign_key_index');
            }
            $table->string('name');
            $table->string('slug')->nullable();
            $table->string('guard_name');
            $table->string('display_name', 100)->nullable();
            $table->string('description')->nullable();
            $table->unsignedInteger('rank')->default(10);
            $table->string('color', 20)->nullable();
            $table->string('icon', 50)->nullable();
            $table->boolean('is_system')->default(false);
            $table->string('status', 20)->default('active');
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('users_count')->default(0);
            $table->timestamps();
            if ($teams || config('permission.testing')) {
                $table->unique([$columnNames['team_foreign_key'], 'name', 'guard_name']);
            } else {
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams): void {
            $table->uuid($pivotPermission);

            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_permissions_model_id_model_type_index');

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->uuid($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_permissions_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotPermission, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_permissions_permission_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['model_has_roles'], static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams): void {
            $table->uuid($pivotRole);

            $table->string('model_type');
            $table->uuid($columnNames['model_morph_key']);
            $table->index([$columnNames['model_morph_key'], 'model_type'], 'model_has_roles_model_id_model_type_index');

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            if ($teams) {
                $table->uuid($columnNames['team_foreign_key']);
                $table->index($columnNames['team_foreign_key'], 'model_has_roles_team_foreign_key_index');

                $table->primary(
                    [$columnNames['team_foreign_key'], $pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            } else {
                $table->primary(
                    [$pivotRole, $columnNames['model_morph_key'], 'model_type'],
                    'model_has_roles_role_model_type_primary'
                );
            }
        });

        Schema::create($tableNames['role_has_permissions'], static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission): void {
            $table->uuid($pivotPermission);
            $table->uuid($pivotRole);

            $table->foreign($pivotPermission)
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->foreign($pivotRole)
                ->references('id')
                ->on($tableNames['roles'])
                ->cascadeOnDelete();

            $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id');
        });

        Schema::create('permission_groups', static function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('permission_group_permission', static function (Blueprint $table) use ($tableNames): void {
            $table->uuid('permission_group_id');
            $table->uuid('permission_id');

            $table->foreign('permission_group_id')
                ->references('id')
                ->on('permission_groups')
                ->cascadeOnDelete();

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->cascadeOnDelete();

            $table->primary(['permission_group_id', 'permission_id'], 'pgp_primary');
        });

        app('cache')
            ->store(config('permission.cache.store') != 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');

        if (empty($tableNames)) {
            throw new \Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        Schema::dropIfExists('permission_group_permission');
        Schema::dropIfExists('permission_groups');
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
