<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name', 80);
            $table->string('slug', 100)->unique();
            $table->text('description')->nullable();
            $table->string('color', 20)->default('neutral');
            $table->boolean('is_visible')->default(true)->index();
            $table->integer('posts_count')->default(0);
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
