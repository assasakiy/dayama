<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('group', 60)->default('general')->index();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string|json|boolean|integer|array
            $table->boolean('is_locked')->default(false);
            $table->boolean('is_env')->default(false);
            $table->text('description')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->timestamps();
        });

        Schema::create('setting_groups', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('key', 60)->unique();
            $table->string('name', 80);
            $table->text('description')->nullable();
            $table->string('icon', 40)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_groups');
        Schema::dropIfExists('settings');
    }
};
