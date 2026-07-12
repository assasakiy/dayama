<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename column category_id to primary_category_id
        Schema::table('posts', function (Blueprint $table): void {
            $table->dropForeign(['category_id']);
            $table->renameColumn('category_id', 'primary_category_id');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->foreign('primary_category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');
        });

        // 2. Create category_post pivot table
        Schema::create('category_post', function (Blueprint $table): void {
            $table->uuid('post_id');
            $table->uuid('category_id');
            $table->timestamps();

            $table->primary(['post_id', 'category_id']);
            
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });

        // 3. Backfill data from primary_category_id to category_post
        DB::table('posts')->whereNotNull('primary_category_id')->orderBy('id')->chunk(100, function ($posts) {
            $pivotData = [];
            $now = now();
            foreach ($posts as $post) {
                $pivotData[] = [
                    'post_id' => $post->id,
                    'category_id' => $post->primary_category_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('category_post')->insert($pivotData);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_post');

        Schema::table('posts', function (Blueprint $table): void {
            $table->dropForeign(['primary_category_id']);
            $table->renameColumn('primary_category_id', 'category_id');
        });

        Schema::table('posts', function (Blueprint $table): void {
            $table->foreign('category_id')
                  ->references('id')->on('categories')
                  ->onDelete('set null');
        });
    }
};
