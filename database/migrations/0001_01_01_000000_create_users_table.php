<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->boolean('is_primary_super_admin')->default(false);
            $table->boolean('is_protected')->default(false);
            $table->boolean('is_verified')->default(false);
            $table->string('name');
            $table->string('username', 60)->unique();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('banner')->nullable();
            $table->json('preferences')->nullable();
            $table->string('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->string('two_factor_backup_status')->nullable();
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->string('status', 20)->default('active');
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['created_at', 'deleted_at']);
        });

        Schema::create('password_reset_tokens', function (Blueprint $table): void {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('avatar')->nullable();
            $table->text('biography')->nullable();
            $table->string('website')->nullable();
            $table->json('social_links')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_emails', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('verification_code', 6)->nullable();
            $table->timestamp('verification_code_expires_at')->nullable();
            $table->timestamp('verification_sent_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('connected_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('provider_name');
            $table->string('provider_id');
            $table->text('access_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['provider_name', 'provider_id']);
        });

        Schema::create('login_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id')->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('device', 100)->nullable();
            $table->string('platform', 60)->nullable();
            $table->string('browser', 60)->nullable();
            $table->string('location', 180)->nullable();
            $table->boolean('is_successful')->default(true);
            $table->string('failure_reason', 60)->nullable();
            $table->timestamp('logged_in_at');
            $table->timestamp('logged_out_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_histories');
        Schema::dropIfExists('connected_accounts');
        Schema::dropIfExists('user_emails');
        Schema::dropIfExists('user_profiles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
