<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add institution_id as nullable first
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->foreignUuid('institution_id')
                ->nullable()
                ->after('id')
                ->constrained('core_institutions')
                ->cascadeOnDelete();
        });

        // 2. Fill existing NULL institution_id before making it NOT NULL
        $firstInstitution = DB::table('core_institutions')->value('id');
        if ($firstInstitution) {
            DB::table('hr_departments')
                ->whereNull('institution_id')
                ->update(['institution_id' => $firstInstitution]);
        }

        // 3. Make institution_id NOT NULL
        DB::statement('ALTER TABLE `hr_departments` MODIFY `institution_id` CHAR(36) NOT NULL');

        // 4. Add kepala_person_id
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->foreignUuid('kepala_person_id')
                ->nullable()
                ->after('description')
                ->constrained('core_persons')
                ->nullOnDelete();
        });

        // 5. Drop slug unique constraint — slugs are now scoped per institution
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->dropUnique('hr_departments_slug_unique');
        });
    }

    public function down(): void
    {
        Schema::table('hr_departments', function (Blueprint $table) {
            $table->dropForeign(['kepala_person_id']);
            $table->dropColumn('kepala_person_id');
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
            $table->unique('slug', 'hr_departments_slug_unique');
        });
    }
};
