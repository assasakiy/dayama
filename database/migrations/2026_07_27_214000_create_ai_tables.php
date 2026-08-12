<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ai_agents')) {
            Schema::create('ai_agents', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('model')->nullable();
                $table->text('system_prompt')->nullable();
                $table->decimal('temperature', 3, 2)->default(0.7);
                $table->integer('max_tokens')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
                $table->index('slug');
            });
        }

        if (!Schema::hasTable('ai_prompts')) {
            Schema::create('ai_prompts', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('agent_id')->nullable();
                $table->string('title');
                $table->text('content');
                $table->string('category')->nullable();
                $table->json('variables')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('agent_id')->references('id')->on('ai_agents')->onDelete('cascade');
                $table->index('agent_id');
                $table->index('category');
            });
        }

        if (!Schema::hasTable('ai_knowledge')) {
            Schema::create('ai_knowledge', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->text('content');
                $table->string('source_type')->nullable();
                $table->uuid('source_id')->nullable();
                $table->json('tags')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('ai_embeddings')) {
            Schema::create('ai_embeddings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('embeddable_type');
                $table->uuid('embeddable_id');
                $table->text('content');
                $table->json('embedding')->nullable();
                $table->string('model')->nullable();
                $table->integer('chunk_index')->default(0);
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['embeddable_type', 'embeddable_id']);
            });
        }

        if (!Schema::hasTable('ai_conversations')) {
            Schema::create('ai_conversations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('agent_id')->nullable();
                $table->uuid('user_id')->nullable();
                $table->string('session_id')->nullable();
                $table->string('title')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('agent_id')->references('id')->on('ai_agents')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('core_users')->onDelete('set null');
                $table->index('agent_id');
                $table->index('user_id');
                $table->index('session_id');
            });
        }

        if (!Schema::hasTable('ai_messages')) {
            Schema::create('ai_messages', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('conversation_id');
                $table->string('role');
                $table->text('content');
                $table->integer('tokens_used')->nullable();
                $table->string('model')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->foreign('conversation_id')->references('id')->on('ai_conversations')->onDelete('cascade');
                $table->index('conversation_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_messages');
        Schema::dropIfExists('ai_conversations');
        Schema::dropIfExists('ai_embeddings');
        Schema::dropIfExists('ai_knowledge');
        Schema::dropIfExists('ai_prompts');
        Schema::dropIfExists('ai_agents');
    }
};