<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('library_book_categories')) {
            Schema::create('library_book_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->uuid('parent_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('parent_id')->references('id')->on('library_book_categories')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('library_book_authors')) {
            Schema::create('library_book_authors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('biography')->nullable();
                $table->string('photo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('library_books')) {
            Schema::create('library_books', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('isbn')->nullable()->unique();
                $table->uuid('author_id')->nullable();
                $table->uuid('category_id')->nullable();
                $table->string('publisher')->nullable();
                $table->integer('published_year')->nullable();
                $table->integer('pages')->nullable();
                $table->text('description')->nullable();
                $table->string('cover_image')->nullable();
                $table->integer('quantity')->default(1);
                $table->integer('available_quantity')->default(1);
                $table->string('location')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('author_id')->references('id')->on('library_book_authors')->nullOnDelete();
                $table->foreign('category_id')->references('id')->on('library_book_categories')->nullOnDelete();

                $table->index('author_id');
                $table->index('category_id');
                $table->index('isbn');
            });
        }

        if (!Schema::hasTable('library_borrowings')) {
            Schema::create('library_borrowings', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('book_id');
                $table->string('borrower_type');
                $table->uuid('borrower_id');
                $table->datetime('borrowed_at');
                $table->datetime('due_at');
                $table->datetime('returned_at')->nullable();
                $table->string('status')->default('borrowed');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('book_id')->references('id')->on('library_books')->cascadeOnDelete();

                $table->index('book_id');
                $table->index(['borrower_type', 'borrower_id']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('library_borrowings');
        Schema::dropIfExists('library_books');
        Schema::dropIfExists('library_book_authors');
        Schema::dropIfExists('library_book_categories');
    }
};
