<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_emails', function (Blueprint $table) {
            $table->string('verification_code', 6)->nullable()->after('is_primary');
            $table->timestamp('verification_code_expires_at')->nullable()->after('verification_code');
            $table->timestamp('verification_sent_at')->nullable()->after('verification_code_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('user_emails', function (Blueprint $table) {
            $table->dropColumn(['verification_code', 'verification_code_expires_at', 'verification_sent_at']);
        });
    }
};
