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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique()->comment('Internal key e.g. welcome-email, reset-password');
            $table->string('name')->comment('Human readable name');
            $table->string('subject')->comment('Email subject with variables');
            $table->text('body')->comment('HTML body with {{var}} placeholders');
            $table->json('variables')->nullable()->comment('Array of available variables for this template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
