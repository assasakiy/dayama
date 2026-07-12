<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_ups', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->uuidMorphs('bankupable');
            $table->string('status', 20)->default('pending'); // pending|completed|failed
            $table->string('filename', 200)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->json('files')->nullable();
            $table->json('metadata')->nullable();
            $table->text('logs')->nullable();
            $table->uuid('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('jobs_queue_status', function (Blueprint $table): void {
            $table->id();
            $table->string('queue', 60)->default('default')->index();
            $table->string('job_class', 160);
            $table->unsignedInteger('tries')->default(0);
            $table->boolean('is_failed')->default(false);
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs_queue_status');
        Schema::dropIfExists('bank_ups');
    }
};
