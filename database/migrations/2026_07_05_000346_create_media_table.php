<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_folders', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuid('parent_id')->nullable()->index();
            $table->string('name', 160);
            $table->string('slug', 200);
            $table->text('description')->nullable();
            $table->string('color', 20)->default('neutral');
            $table->unsignedInteger('files_count')->default(0);
            $table->unsignedBigInteger('total_size')->default(0);
            $table->uuid('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('parent_id')
                  ->references('id')->on('media_folders')
                  ->onDelete('cascade');

            $table->index(['parent_id', 'slug']);
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();

            $table->uuid('folder_id')->nullable();
            $table->uuidMorphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->text('description')->nullable();
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('disk_path')->nullable();
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('optimized_conversions')->nullable();
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->uuid('uploaded_by')->nullable();

            $table->nullableTimestamps();
            $table->softDeletes();
            
            $table->foreign('folder_id')
                  ->references('id')->on('media_folders')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('media_folders');
    }
};
