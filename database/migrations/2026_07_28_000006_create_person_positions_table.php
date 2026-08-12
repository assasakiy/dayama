<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('person_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('position_id');
            $table->uuid('institution_id')->nullable();
            $table->string('nomor_induk', 50)->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status', 20)->default('aktif');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('position_id')->references('id')->on('positions')->cascadeOnDelete();
            $table->foreign('institution_id')->references('id')->on('institutions')->nullOnDelete();

            $table->index(['institution_id', 'position_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_positions');
    }
};
