<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_students', function (Blueprint $table) {
            // Drop old global unique constraints
            $table->dropUnique('students_nis_unique');
            $table->dropUnique('students_nisn_unique');

            // Drop kelas column — source of truth is academic_classroom_student
            $table->dropColumn('kelas');
        });

        // Add new composite constraints
        Schema::table('academic_students', function (Blueprint $table) {
            $table->unique(['nis', 'institution_id'], 'student_nis_per_institution');
            $table->unique(['person_id', 'institution_id'], 'student_person_per_institution');
        });
    }

    public function down(): void
    {
        Schema::table('academic_students', function (Blueprint $table) {
            $table->dropUnique('student_nis_per_institution');
            $table->dropUnique('student_person_per_institution');
        });

        Schema::table('academic_students', function (Blueprint $table) {
            $table->string('kelas', 30)->nullable();
            $table->unique('nisn', 'students_nisn_unique');
            $table->unique('nis', 'students_nis_unique');
        });
    }
};
