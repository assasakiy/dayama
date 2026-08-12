<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 120);
            $table->string('slug', 150)->unique();
            $table->string('title', 160)->nullable();
            $table->text('description')->nullable();
            $table->string('color', 20)->default('neutral');
            $table->string('icon')->nullable();
            $table->string('seo_title', 160)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->uuid('parent_id')->nullable()->index();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_visible')->default(true)->index();
            $table->integer('posts_count')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
