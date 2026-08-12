<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('education_levels', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('person_educations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('education_level_id');
            $table->string('institution_name');
            $table->string('jurusan')->nullable();
            $table->unsignedSmallInteger('tahun_masuk')->nullable();
            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->string('status', 30)->default('selesai');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('education_level_id')->references('id')->on('education_levels');
        });

        Schema::create('professions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('person_professions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('profession_id');
            $table->boolean('is_primary')->default(false);
            $table->date('mulai')->nullable();
            $table->date('selesai')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('profession_id')->references('id')->on('professions');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_professions');
        Schema::dropIfExists('professions');
        Schema::dropIfExists('person_educations');
        Schema::dropIfExists('education_levels');
    }
};
