<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reading_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('post_id')->constrained()->cascadeOnDelete();
            $table->string('identity_key');
            $table->foreignUuid('user_id')->nullable()->constrained()->nullOnDelete();
            
            $table->timestamp('first_read_at')->useCurrent();
            $table->timestamp('last_read_at')->useCurrent();
            $table->unsignedBigInteger('read_count')->default(1);

            $table->timestamps();

            $table->unique(['post_id', 'identity_key']);
            $table->index('identity_key');
            $table->index(['identity_key', 'last_read_at']); // DESC index not universally supported in Schema builder syntax simply, so we rely on DB level optimization
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_histories');
    }
};
