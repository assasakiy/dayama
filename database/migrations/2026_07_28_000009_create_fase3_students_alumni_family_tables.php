<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $personsTable = Schema::hasTable('core_persons') ? 'core_persons' : 'persons';
        $instTable = Schema::hasTable('core_institutions') ? 'core_institutions' : 'institutions';

        if (! Schema::hasTable('academic_students') && ! Schema::hasTable('students')) {
            $tableName = Schema::hasTable('core_users') ? 'academic_students' : 'students';
            Schema::create($tableName, function (Blueprint $table) use ($personsTable, $instTable) {
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

                $table->foreign('person_id')->references('id')->on($personsTable)->cascadeOnDelete();
                $table->foreign('institution_id')->references('id')->on($instTable);
            });
        }

        $studentsTable = Schema::hasTable('academic_students') ? 'academic_students' : 'students';

        if (! Schema::hasTable('academic_alumni') && ! Schema::hasTable('alumni')) {
            $tableName = Schema::hasTable('core_users') ? 'academic_alumni' : 'alumni';
            Schema::create($tableName, function (Blueprint $table) use ($studentsTable) {
                $table->uuid('id')->primary();
                $table->uuid('student_id');
                $table->unsignedSmallInteger('tahun_lulus');
                $table->timestamps();

                $table->foreign('student_id')->references('id')->on($studentsTable)->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('academic_student_transfers') && ! Schema::hasTable('student_transfers')) {
            $tableName = Schema::hasTable('core_users') ? 'academic_student_transfers' : 'student_transfers';
            Schema::create($tableName, function (Blueprint $table) use ($studentsTable, $instTable) {
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

                $table->foreign('student_id')->references('id')->on($studentsTable)->cascadeOnDelete();
                $table->foreign('from_institution_id')->references('id')->on($instTable)->nullOnDelete();
                $table->foreign('to_institution_id')->references('id')->on($instTable)->nullOnDelete();
            });
        }

        if (! Schema::hasTable('hr_employment_histories') && ! Schema::hasTable('employment_histories')) {
            $tableName = Schema::hasTable('core_users') ? 'hr_employment_histories' : 'employment_histories';
            Schema::create($tableName, function (Blueprint $table) use ($personsTable, $instTable) {
                $table->uuid('id')->primary();
                $table->uuid('person_id');
                $table->uuid('institution_id')->nullable();
                $table->string('jabatan');
                $table->date('mulai')->nullable();
                $table->date('selesai')->nullable();
                $table->timestamps();

                $table->foreign('person_id')->references('id')->on($personsTable)->cascadeOnDelete();
                $table->foreign('institution_id')->references('id')->on($instTable)->nullOnDelete();
            });
        }

        if (! Schema::hasTable('crm_relationship_types') && ! Schema::hasTable('relationship_types')) {
            $tableName = Schema::hasTable('core_users') ? 'crm_relationship_types' : 'relationship_types';
            Schema::create($tableName, function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('nama')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('crm_family_relations') && ! Schema::hasTable('family_relations')) {
            $tableName = Schema::hasTable('core_users') ? 'crm_family_relations' : 'family_relations';
            $relTypesTable = Schema::hasTable('crm_relationship_types') ? 'crm_relationship_types' : (Schema::hasTable('relationship_types') ? 'relationship_types' : 'crm_relationship_types');
            Schema::create($tableName, function (Blueprint $table) use ($personsTable, $relTypesTable) {
                $table->uuid('id')->primary();
                $table->uuid('person_id');
                $table->uuid('related_person_id');
                $table->uuid('relationship_type_id');
                $table->timestamps();

                $table->foreign('person_id')->references('id')->on($personsTable)->cascadeOnDelete();
                $table->foreign('related_person_id')->references('id')->on($personsTable)->cascadeOnDelete();
                $table->foreign('relationship_type_id')->references('id')->on($relTypesTable);
            });
        }
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
