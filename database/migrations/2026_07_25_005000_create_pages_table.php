<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stat_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->json('items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('faqs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('umum');
            $table->string('group')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('ctas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->string('button_text')->default('Selengkapnya');
            $table->string('button_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');           // "Home", "Pendidikan", dll.
            $table->string('slug')->unique(); // "home", "pendidikan"
            $table->json('sections')->nullable(); // Data JSON per section/tab
            $table->uuid('cta_id')->nullable();
            $table->uuid('stat_group_id')->nullable();
            $table->string('faq_category')->nullable()->comment('Menampilkan list FAQ berdasar kategori ini');
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->foreign('cta_id')->references('id')->on('ctas')->onDelete('set null');
            $table->foreign('stat_group_id')->references('id')->on('stat_groups')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('ctas');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('stat_groups');
    }
};
