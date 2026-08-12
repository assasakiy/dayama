<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('institution_id');
            $table->string('nis', 30)->unique();
            $table->string('nisn', 20)->nullable()->unique();
            $table->string('angkatan', 10);
            $table->string('kelas', 30)->nullable();
            $table->string('status', 20)->default('aktif');
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('tempat_tinggal', 30)->nullable();
            $table->string('nomor_kk', 20)->nullable();
            $table->string('nomor_kip', 20)->nullable();
            $table->string('cita_cita')->nullable();
            $table->string('hobi')->nullable();
            $table->string('foto')->nullable();
            $table->unsignedSmallInteger('waktu_tempuh_menit')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('institution_id')->references('id')->on('institutions');
        });

        Schema::create('alumni', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->unsignedSmallInteger('tahun_lulus');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
        });

        Schema::create('student_transfers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('from_institution_id')->nullable();
            $table->uuid('to_institution_id')->nullable();
            $table->string('jenis', 10);
            $table->string('alasan')->nullable();
            $table->string('nomor_dokumen_emis')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('status', 20)->default('diajukan');
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('from_institution_id')->references('id')->on('institutions')->nullOnDelete();
            $table->foreign('to_institution_id')->references('id')->on('institutions')->nullOnDelete();
        });

        Schema::create('employment_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('institution_id')->nullable();
            $table->string('jabatan');
            $table->date('mulai')->nullable();
            $table->date('selesai')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('institution_id')->references('id')->on('institutions')->nullOnDelete();
        });

        Schema::create('relationship_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('family_relations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('related_person_id');
            $table->uuid('relationship_type_id');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('related_person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('relationship_type_id')->references('id')->on('relationship_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('family_relations');
        Schema::dropIfExists('relationship_types');
        Schema::dropIfExists('employment_histories');
        Schema::dropIfExists('student_transfers');
        Schema::dropIfExists('alumni');
        Schema::dropIfExists('students');
    }
};
