<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->string('icon', 50)->nullable();
            $table->timestamps();
        });

        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('contact_type_id');
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('contact_type_id')->references('id')->on('contact_types');
        });

        Schema::create('address_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('address_type_id');
            $table->text('alamat');
            $table->string('provinsi')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('address_type_id')->references('id')->on('address_types');
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->string('slug')->unique();
            $table->string('kategori')->nullable();
            $table->timestamps();
        });

        Schema::create('person_skills', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('skill_id');
            $table->unsignedTinyInteger('level')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('skill_id')->references('id')->on('skills');
            $table->unique(['person_id', 'skill_id']);
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('person_languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('language_id');
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('language_id')->references('id')->on('languages');
            $table->unique(['person_id', 'language_id']);
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->string('nama');
            $table->string('penerbit')->nullable();
            $table->string('nomor')->nullable();
            $table->date('tanggal_terbit')->nullable();
            $table->date('expired_at')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
        });

        // PTK (Pendidik dan Tenaga Kependidikan)
        Schema::create('employment_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('institution_id');
            $table->uuid('employment_status_id')->nullable();
            $table->string('nuptk', 20)->nullable()->unique();
            $table->string('nip', 20)->nullable();
            $table->boolean('sudah_sertifikasi')->default(false);
            $table->string('nomor_sertifikat_pendidik')->nullable();
            $table->unsignedTinyInteger('jam_mengajar_per_minggu')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('employment_status_id')->references('id')->on('employment_statuses')->nullOnDelete();
            $table->unique(['person_id', 'institution_id']);
        });

        // Akademik: Tahun Ajaran, Kelas, Mata Pelajaran
        Schema::create('academic_years', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama')->unique();
            $table->string('kode', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institution_id');
            $table->uuid('academic_year_id');
            $table->uuid('wali_kelas_person_id')->nullable();
            $table->string('nama');
            $table->string('tingkat')->nullable();
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions');
            $table->foreign('academic_year_id')->references('id')->on('academic_years');
            $table->foreign('wali_kelas_person_id')->references('id')->on('persons')->nullOnDelete();
        });

        Schema::create('classroom_student', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('classroom_id');
            $table->uuid('student_id');
            $table->timestamps();

            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->unique(['classroom_id', 'student_id']);
        });

        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('person_id');
            $table->uuid('subject_id');
            $table->uuid('classroom_id');
            $table->unsignedTinyInteger('jam_per_minggu')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('persons')->cascadeOnDelete();
            $table->foreign('subject_id')->references('id')->on('subjects');
            $table->foreign('classroom_id')->references('id')->on('classrooms')->cascadeOnDelete();
            $table->unique(['person_id', 'subject_id', 'classroom_id']);
        });

        // Kontak institusi — reuse contact_types master
        Schema::create('institution_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institution_id');
            $table->uuid('contact_type_id');
            $table->string('value');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions')->cascadeOnDelete();
            $table->foreign('contact_type_id')->references('id')->on('contact_types');
        });

        // Legalitas institusi
        Schema::create('institution_legalities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institution_id')->unique();
            $table->string('nspp', 20)->nullable()->unique();
            $table->string('npsn', 20)->nullable()->unique();
            $table->string('kode_registrasi')->nullable();
            $table->string('nomor_ijop')->nullable();
            $table->date('tanggal_ijop')->nullable();
            $table->string('nomor_akta_yayasan')->nullable();
            $table->string('npwp', 20)->nullable();
            $table->unsignedSmallInteger('tahun_berdiri_masehi')->nullable();
            $table->unsignedSmallInteger('tahun_berdiri_hijriyah')->nullable();
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions')->cascadeOnDelete();
        });

        // Alamat institusi
        Schema::create('institution_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('institution_id')->unique();
            $table->string('alamat_jalan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten_kota')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();

            $table->foreign('institution_id')->references('id')->on('institutions')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institution_addresses');
        Schema::dropIfExists('institution_legalities');
        Schema::dropIfExists('institution_contacts');
        Schema::dropIfExists('teaching_assignments');
        Schema::dropIfExists('classroom_student');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('employment_statuses');
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('person_languages');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('person_skills');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('address_types');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('contact_types');
    }
};
