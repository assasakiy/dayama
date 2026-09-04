<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = collect(\Illuminate\Support\Facades\DB::select('SHOW INDEXES FROM academic_students'))->pluck('Key_name')->all();

        Schema::table('academic_students', function (Blueprint $table) use ($indexes) {
            // Drop old global unique constraints
            if (in_array('students_nis_unique', $indexes, true)) {
                $table->dropUnique('students_nis_unique');
            } elseif (in_array('academic_students_nis_unique', $indexes, true)) {
                $table->dropUnique('academic_students_nis_unique');
            }

            if (in_array('students_nisn_unique', $indexes, true)) {
                $table->dropUnique('students_nisn_unique');
            } elseif (in_array('academic_students_nisn_unique', $indexes, true)) {
                $table->dropUnique('academic_students_nisn_unique');
            }

            // Drop kelas column — source of truth is academic_classroom_student
            if (Schema::hasColumn('academic_students', 'kelas')) {
                $table->dropColumn('kelas');
            }
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
