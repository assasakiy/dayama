<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('landing_hero_sections')) {
            Schema::create('landing_hero_sections', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('page_id')->nullable();
                $table->string('title');
                $table->string('subtitle')->nullable();
                $table->text('description')->nullable();
                $table->string('background_image')->nullable();
                $table->string('background_color')->nullable();
                $table->string('cta_text')->nullable();
                $table->string('cta_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('page_id')->references('id')->on('landing_pages')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('landing_testimonials')) {
            Schema::create('landing_testimonials', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('title')->nullable();
                $table->string('avatar')->nullable();
                $table->text('content');
                $table->integer('rating')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('landing_partners')) {
            Schema::create('landing_partners', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('logo')->nullable();
                $table->string('website')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('landing_galleries')) {
            Schema::create('landing_galleries', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('page_id')->nullable();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('image');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('page_id')->references('id')->on('landing_pages')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_galleries');
        Schema::dropIfExists('landing_partners');
        Schema::dropIfExists('landing_testimonials');
        Schema::dropIfExists('landing_hero_sections');
    }
};
