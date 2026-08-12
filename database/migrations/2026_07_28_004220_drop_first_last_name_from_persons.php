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
        Schema::table('core_persons', function (Blueprint $table) {
            $table->dropColumn(['nama_depan', 'nama_belakang']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_persons', function (Blueprint $table) {
            $table->string('nama_depan')->nullable();
            $table->string('nama_belakang')->nullable();
        });
    }
};
