<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_views', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->char('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('referrer', 255)->nullable();
            $table->string('source', 60)->nullable()->index(); // organic|social|direct|email|...
            $table->unsignedInteger('dwell_time')->default(0);
            $table->unsignedInteger('scroll_depth')->default(0);
            $table->timestamp('viewed_at')->useCurrent();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->index(['post_id', 'viewed_at']);
            $table->index(['post_id', 'ip_address', 'viewed_at'], 'post_views_unique_index');
        });

        Schema::create('reactions', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('post_id')->index();
            $table->uuid('user_id')->nullable()->index();
            $table->char('ip_address', 45)->nullable();
            $table->string('type', 30)->default('like');
            $table->timestamps();

            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');

            $table->index(['post_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
        Schema::dropIfExists('post_views');
    }
};
