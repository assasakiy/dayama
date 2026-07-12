<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', static function (Blueprint $table): void {
            $table->string('slug')->nullable()->unique()->after('name');
            $table->string('color', 20)->nullable()->after('description');
            $table->string('icon', 50)->nullable()->after('color');
            $table->boolean('is_system')->default(false)->after('icon');
            $table->string('status', 20)->default('active')->after('is_system');
        });

        Schema::table('permissions', static function (Blueprint $table): void {
            $table->string('module', 60)->nullable()->after('name');
            $table->string('action', 60)->nullable()->after('module');
            $table->string('scope', 30)->nullable()->after('action');
            $table->text('description')->nullable()->after('scope');
            $table->unsignedInteger('sort_order')->default(0)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('roles', static function (Blueprint $table): void {
            $table->dropColumn(['slug', 'color', 'icon', 'is_system', 'status']);
        });

        Schema::table('permissions', static function (Blueprint $table): void {
            $table->dropColumn(['module', 'action', 'scope', 'description', 'sort_order']);
        });
    }
};
