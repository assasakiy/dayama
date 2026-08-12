<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            // Drop global unique index on nuptk if exists
            $indexes = collect(Schema::getIndexes('hr_employees'))->pluck('name');
            if ($indexes->contains('employee_profiles_nuptk_unique')) {
                $table->dropUnique('employee_profiles_nuptk_unique');
            }
            if ($indexes->contains('hr_employees_nuptk_unique')) {
                $table->dropUnique('hr_employees_nuptk_unique');
            }

            // Add composite unique indexes per institution
            $table->unique(['nuptk', 'institution_id'], 'hr_employees_nuptk_institution_unique');
            $table->unique(['nip', 'institution_id'], 'hr_employees_nip_institution_unique');
        });
    }

    public function down(): void
    {
        Schema::table('hr_employees', function (Blueprint $table) {
            $table->dropUnique('hr_employees_nuptk_institution_unique');
            $table->dropUnique('hr_employees_nip_institution_unique');
            $table->unique('nuptk', 'employee_profiles_nuptk_unique');
        });
    }
};
