<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('academic_student_statuses')) {
            Schema::create('academic_student_statuses', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('color')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('academic_semesters')) {
            Schema::create('academic_semesters', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('academic_year_id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('academic_year_id')->references('id')->on('academic_academic_years')->onDelete('cascade');
                $table->index('academic_year_id');
            });
        }

        if (!Schema::hasTable('academic_classes')) {
            Schema::create('academic_classes', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->uuid('academic_year_id');
                $table->uuid('education_level_id')->nullable();
                $table->uuid('homeroom_teacher_id')->nullable();
                $table->integer('capacity')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('academic_year_id')->references('id')->on('academic_academic_years');
                $table->foreign('education_level_id')->references('id')->on('core_education_levels');
                $table->index('academic_year_id');
                $table->index('education_level_id');
            });
        }

        if (!Schema::hasTable('academic_subject_groups')) {
            Schema::create('academic_subject_groups', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('academic_student_enrollments')) {
            Schema::create('academic_student_enrollments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('student_id');
                $table->uuid('academic_year_id');
                $table->uuid('semester_id')->nullable();
                $table->uuid('class_id')->nullable();
                $table->uuid('status_id')->nullable();
                $table->date('entry_date');
                $table->date('exit_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('student_id')->references('id')->on('academic_students')->onDelete('cascade');
                $table->foreign('academic_year_id')->references('id')->on('academic_academic_years');
                $table->foreign('semester_id')->references('id')->on('academic_semesters');
                $table->foreign('class_id')->references('id')->on('academic_classes');
                $table->foreign('status_id')->references('id')->on('academic_student_statuses');
                $table->index('student_id');
                $table->index('academic_year_id');
                $table->index('class_id');
                $table->index('status_id');
            });
        }

        if (!Schema::hasTable('academic_grades')) {
            Schema::create('academic_grades', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('student_enrollment_id');
                $table->uuid('subject_id');
                $table->uuid('semester_id')->nullable();
                $table->decimal('score', 5, 2)->nullable();
                $table->string('grade_letter')->nullable();
                $table->text('notes')->nullable();
                $table->uuid('graded_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('student_enrollment_id')->references('id')->on('academic_student_enrollments')->onDelete('cascade');
                $table->foreign('subject_id')->references('id')->on('academic_subjects');
                $table->foreign('semester_id')->references('id')->on('academic_semesters');
                $table->foreign('graded_by')->references('id')->on('core_users');
                $table->index('student_enrollment_id');
                $table->index('subject_id');
            });
        }

        if (!Schema::hasTable('academic_attendance')) {
            Schema::create('academic_attendance', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('student_enrollment_id');
                $table->date('date');
                $table->string('status');
                $table->text('notes')->nullable();
                $table->uuid('recorded_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('student_enrollment_id')->references('id')->on('academic_student_enrollments')->onDelete('cascade');
                $table->foreign('recorded_by')->references('id')->on('core_users');
                $table->index('student_enrollment_id');
                $table->index('date');
            });
        }

        if (!Schema::hasTable('academic_graduations')) {
            Schema::create('academic_graduations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('student_enrollment_id')->unique();
                $table->date('graduation_date');
                $table->string('certificate_number')->nullable();
                $table->decimal('final_score', 5, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('student_enrollment_id')->references('id')->on('academic_student_enrollments')->onDelete('cascade');
                $table->index('student_enrollment_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_graduations');
        Schema::dropIfExists('academic_attendance');
        Schema::dropIfExists('academic_grades');
        Schema::dropIfExists('academic_student_enrollments');
        Schema::dropIfExists('academic_subject_groups');
        Schema::dropIfExists('academic_classes');
        Schema::dropIfExists('academic_semesters');
        Schema::dropIfExists('academic_student_statuses');
    }
};
