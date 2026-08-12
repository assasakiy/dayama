<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private bool $createdEmployees = false;

    public function up(): void
    {
        if (!Schema::hasTable('hr_employees')) {
            $this->createdEmployees = true;
            Schema::create('hr_employees', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('person_id');
                $table->string('employee_number')->unique();
                $table->uuid('employment_status_id')->nullable();
                $table->uuid('position_id')->nullable();
                $table->uuid('department_id')->nullable();
                $table->uuid('division_id')->nullable();
                $table->date('hire_date')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('person_id')->references('id')->on('core_persons')->cascadeOnDelete();
                $table->foreign('employment_status_id')->references('id')->on('hr_employment_statuses')->nullOnDelete();
                $table->foreign('position_id')->references('id')->on('hr_positions')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('hr_departments')) {
            Schema::create('hr_departments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('code')->nullable();
                $table->text('description')->nullable();
                $table->uuid('head_employee_id')->nullable();
                $table->uuid('parent_id')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('head_employee_id')->references('id')->on('hr_employees')->nullOnDelete();
                $table->foreign('parent_id')->references('id')->on('hr_departments')->nullOnDelete();
                $table->index('parent_id');
            });
        }

        if (!Schema::hasTable('hr_divisions')) {
            Schema::create('hr_divisions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('code')->nullable();
                $table->uuid('department_id');
                $table->text('description')->nullable();
                $table->uuid('head_employee_id')->nullable();
                $table->integer('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('department_id')->references('id')->on('hr_departments')->cascadeOnDelete();
                $table->foreign('head_employee_id')->references('id')->on('hr_employees')->nullOnDelete();
                $table->index('department_id');
            });
        }

        if ($this->createdEmployees) {
            Schema::table('hr_employees', function (Blueprint $table) {
                $table->foreign('department_id')->references('id')->on('hr_departments')->nullOnDelete();
                $table->foreign('division_id')->references('id')->on('hr_divisions')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('hr_leave_requests')) {
            Schema::create('hr_leave_requests', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('employee_id');
                $table->string('leave_type');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('reason');
                $table->string('status')->default('pending');
                $table->uuid('approved_by')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
                $table->foreign('approved_by')->references('id')->on('hr_employees')->nullOnDelete();
                $table->index('employee_id');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('hr_attendances')) {
            Schema::create('hr_attendances', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('employee_id');
                $table->date('date');
                $table->dateTime('check_in')->nullable();
                $table->dateTime('check_out')->nullable();
                $table->string('status');
                $table->text('notes')->nullable();
                $table->uuid('recorded_by')->nullable();
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('hr_employees')->cascadeOnDelete();
                $table->foreign('recorded_by')->references('id')->on('core_users')->nullOnDelete();
                $table->index('employee_id');
                $table->index('date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_attendances');
        Schema::dropIfExists('hr_leave_requests');
        Schema::dropIfExists('hr_divisions');
        Schema::dropIfExists('hr_departments');

        if ($this->createdEmployees) {
            Schema::dropIfExists('hr_employees');
        }
    }
};
