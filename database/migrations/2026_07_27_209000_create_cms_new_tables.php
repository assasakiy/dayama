<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cms_menus')) {
            Schema::create('cms_menus', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('location');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index('location');
            });
        }

        if (!Schema::hasTable('cms_menu_items')) {
            Schema::create('cms_menu_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('menu_id');
                $table->uuid('parent_id')->nullable();
                $table->string('label');
                $table->string('url')->nullable();
                $table->uuid('page_id')->nullable();
                $table->string('target')->default('_self');
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('menu_id')->references('id')->on('cms_menus')->onDelete('cascade');
                $table->foreign('parent_id')->references('id')->on('cms_menu_items')->onDelete('cascade');
                $table->foreign('page_id')->references('id')->on('cms_posts')->onDelete('set null');
                $table->index('menu_id');
                $table->index('parent_id');
            });
        }

        if (!Schema::hasTable('cms_announcements')) {
            Schema::create('cms_announcements', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->text('content');
                $table->text('excerpt')->nullable();
                $table->timestamp('published_at')->nullable();
                $table->uuid('author_id')->nullable();
                $table->boolean('is_published')->default(false);
                $table->boolean('is_pinned')->default(false);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('author_id')->references('id')->on('core_users')->onDelete('set null');
                $table->index('published_at');
                $table->index('is_published');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_announcements');
        Schema::dropIfExists('cms_menu_items');
        Schema::dropIfExists('cms_menus');
    }
};
