<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('parent_id')->nullable()->index();
            $table->unsignedTinyInteger('depth')->default(0);
            $table->uuid('author_id')->nullable(); // registered user author
            $table->string('identity_key')->nullable();
            $table->boolean('created_as_guest')->default(false);
            $table->string('guest_name', 100)->nullable();
            $table->string('guest_email', 160)->nullable();
            $table->string('guest_website')->nullable();
            $table->string('guest_ip', 45)->nullable();
            $table->string('guest_user_agent')->nullable();
            $table->text('content');
            $table->string('status', 20)->default('pending')->index(); // pending|approved|spam|rejected|trashed
            $table->unsignedSmallInteger('moderation_score')->nullable();
            $table->json('moderation_flags')->nullable();
            $table->unsignedInteger('likes_count')->default(0);
            $table->unsignedInteger('replies_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('moderated_at')->nullable();
            $table->uuid('moderated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->foreign('parent_id')
                  ->references('id')->on('comments')
                  ->onDelete('cascade');

            $table->foreign('author_id')
                  ->references('id')->on('users')
                  ->onDelete('set null');

            $table->index(['post_id', 'status', 'created_at']);
        });

        Schema::create('comment_reactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('comment_id')->constrained()->cascadeOnDelete();
            $table->string('identity_key')->index();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->timestamps();
            $table->unique(['comment_id', 'identity_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_reactions');
        Schema::dropIfExists('comments');
    }
};
