<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        Schema::dropIfExists('post_revisions');
    }
};
