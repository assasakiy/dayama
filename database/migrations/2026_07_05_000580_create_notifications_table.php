<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('type', 120);
            $table->uuidMorphs('notifiable');
            $table->json('data');
            $table->text('message')->nullable();
            $table->string('link')->nullable();
            $table->string('level', 10)->default('info');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('email', 160)->unique();
            $table->string('name', 120)->nullable();
            $table->string('status', 20)->default('subscribed')->index();
            $table->string('source', 60)->default('website')->index();
            $table->json('preferences')->nullable();
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('verification_token', 80)->nullable();
            $table->char('ip_address', 45)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('notifications');
    }
};
