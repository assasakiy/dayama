<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->uuid('id')->primary();

            // Authors / ownership
            $table->uuid('author_id')->index();
            $table->uuid('category_id')->nullable()->index();

            // Identity
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 320)->nullable();
            $table->longText('content')->nullable();
            $table->string('content_format', 20)->default('tiptap'); // tiptap|markdown|html

            // Media
            $table->string('thumbnail')->nullable();
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();

            // SEO fields
            $table->string('seo_title', 200)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url', 300)->nullable();
            $table->text('og_data')->nullable(); // JSON: twitter_card_type, custom og image, etc
            $table->text('json_ld')->nullable(); // Optional custom schema override
            $table->string('robots', 80)->default('index, follow');

            // Publishing
            $table->string('status', 20)->default('draft')->index(); // draft|scheduled|published|archived|trashed
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_sticky')->default(false)->index();
            $table->boolean('allow_comments')->default(true);
            $table->boolean('is_pinned')->default(false);

            // Engagement / computed metrics
            $table->unsignedBigInteger('views_count')->default(0)->index();
            $table->unsignedInteger('reading_time')->default(0); // minutes
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('comments_count')->default(0);
            $table->unsignedInteger('shares_count')->default(0);
            $table->unsignedInteger('reactions_count')->default(0);

            // Versioning
            $table->uuid('parent_revision_id')->nullable();
            $table->unsignedInteger('revision_number')->default(1);

            // Metadata
            $table->string('language', 8)->default('en');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('author_id')
                  ->references('id')->on('users')
                  ->onDelete('restrict');

            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');

            $table->index(['status', 'published_at', 'is_featured'], 'posts_publish_index');
            $table->index(['status', 'is_sticky'], 'posts_sticky_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
