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
            $table->uuid('primary_category_id')->nullable()->index();

            // Identity
            $table->string('title', 200);
            $table->string('slug', 220)->unique();
            $table->string('excerpt', 320)->nullable();
            $table->longText('content')->nullable();
            $table->string('content_format', 20)->default('tiptap'); // tiptap|markdown|html

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
            $table->json('reactions_breakdown')->nullable();

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

            $table->foreign('primary_category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');

            $table->index(['status', 'published_at', 'is_featured'], 'posts_publish_index');
            $table->index(['status', 'is_sticky'], 'posts_sticky_index');
        });

        Schema::create('category_post', function (Blueprint $table): void {
            $table->uuid('category_id')->index();
            $table->uuid('post_id')->index();
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');

            $table->primary(['category_id', 'post_id']);
        });

        Schema::create('post_revisions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('author_id')->nullable();
            $table->string('title', 200);
            $table->string('slug', 220);
            $table->string('excerpt', 320)->nullable();
            $table->longText('content');
            $table->text('change_summary')->nullable();
            $table->unsignedInteger('revision_number');
            $table->boolean('is_autosave')->default(false);
            $table->json('metadata')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->foreign('author_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->index(['post_id', 'revision_number']);
        });

        Schema::create('post_tag', function (Blueprint $table): void {
            $table->uuid('post_id');
            $table->uuid('tag_id');
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->foreign('tag_id')
                  ->references('id')->on('tags')
                  ->onDelete('cascade');

            $table->primary(['post_id', 'tag_id']);
            $table->index('tag_id');
        });

        Schema::create('post_views', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('identity_key')->index();
            $table->char('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('source', 60)->nullable()->index(); // organic|social|direct|email|...
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->boolean('is_unique')->default(false)->index();
            $table->date('view_date')->index();
            $table->unsignedInteger('dwell_time')->default(0);
            $table->unsignedInteger('scroll_depth')->default(0);
            $table->timestamp('viewed_at')->useCurrent();
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->index(['post_id', 'viewed_at']);
            $table->index(['post_id', 'ip_address', 'viewed_at'], 'post_views_unique_index');
            $table->unique(['post_id', 'identity_key', 'view_date'], 'view_unique_identity_window');
        });

        Schema::create('reactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('identity_key')->index();
            $table->char('ip_address', 45)->nullable();
            $table->string('type', 30)->default('like');
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->index(['post_id', 'type']);
            $table->unique(['post_id', 'identity_key', 'type'], 'reaction_unique_identity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('post_views');
        Schema::dropIfExists('post_tag');
        Schema::dropIfExists('post_revisions');
        Schema::dropIfExists('category_post');
        Schema::dropIfExists('posts');
    }
};
