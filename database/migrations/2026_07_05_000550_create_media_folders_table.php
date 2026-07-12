<?php

declare(strict_types=1);

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

        Schema::table('media', function (Blueprint $table): void {
            $table->uuid('folder_id')->nullable()->index()->after('id');
            $table->string('alt_text')->nullable()->after('name');
            $table->string('caption')->nullable()->after('alt_text');
            $table->text('description')->nullable()->after('caption');
            $table->string('disk_path')->nullable()->after('disk');
            $table->unsignedInteger('width')->nullable()->after('size');
            $table->unsignedInteger('height')->nullable()->after('width');
            $table->json('optimized_conversions')->nullable()->after('generated_conversions');
            $table->uuid('uploaded_by')->nullable()->after('order_column');
            $table->softDeletes();

            $table->foreign('folder_id')
                  ->references('id')->on('media_folders')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table): void {
            $table->dropForeign(['folder_id']);
            $table->dropColumn([
                'folder_id', 'alt_text', 'caption', 'description',
                'disk_path', 'width', 'height', 'optimized_conversions',
                'uploaded_by', 'deleted_at',
            ]);
        });
        Schema::dropIfExists('media_folders');
    }
};
