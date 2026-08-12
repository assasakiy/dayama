<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('core_religions')) {
            Schema::create('core_religions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
                $table->index('slug');
                $table->index('sort_order');
            });
        }

        if (!Schema::hasTable('core_genders')) {
            Schema::create('core_genders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
                $table->index('slug');
                $table->index('sort_order');
            });
        }

        if (!Schema::hasTable('core_marital_statuses')) {
            Schema::create('core_marital_statuses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
                $table->index('slug');
                $table->index('sort_order');
            });
        }

        if (!Schema::hasTable('core_education_levels')) {
            Schema::create('core_education_levels', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->integer('level')->nullable();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
                $table->index('slug');
                $table->index('sort_order');
            });
        }

        if (!Schema::hasTable('core_relationship_types')) {
            Schema::create('core_relationship_types', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->uuid('opposite_id')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_family')->default(false);
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('opposite_id')->references('id')->on('core_relationship_types')->onDelete('set null');
                $table->index('is_active');
                $table->index('slug');
                $table->index('sort_order');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('core_relationship_types');
        Schema::dropIfExists('core_education_levels');
        Schema::dropIfExists('core_marital_statuses');
        Schema::dropIfExists('core_genders');
        Schema::dropIfExists('core_religions');
    }
};
