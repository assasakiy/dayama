<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop global UNIQUE on nik if still exists
        $indexExists = DB::select("SHOW INDEX FROM `core_persons` WHERE Key_name = 'persons_nik_unique'");
        if (!empty($indexExists)) {
            Schema::table('core_persons', function (Blueprint $table) {
                $table->dropUnique('persons_nik_unique');
            });
        }

        // 2. Fill existing NULL institution_id before making it NOT NULL
        $firstInstitution = DB::table('core_institutions')->value('id');
        if ($firstInstitution) {
            DB::table('core_persons')
                ->whereNull('institution_id')
                ->update(['institution_id' => $firstInstitution]);
        }

        // 3. Make institution_id NOT NULL — raw SQL since change() doesn't support foreign modification
        DB::statement('ALTER TABLE `core_persons` MODIFY `institution_id` CHAR(36) NOT NULL');

        // 4. Drop old FK and re-add with CASCADE
        $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'core_persons' AND CONSTRAINT_NAME = 'core_persons_institution_id_foreign' AND TABLE_SCHEMA = DATABASE()");
        if (!empty($fkExists)) {
            Schema::table('core_persons', function (Blueprint $table) {
                $table->dropForeign('core_persons_institution_id_foreign');
                $table->foreign('institution_id')->references('id')->on('core_institutions')->cascadeOnDelete();
            });
        }

        // 5. Add composite unique — skip if already exists
        $compositeIndex = DB::select("SHOW INDEX FROM `core_persons` WHERE Key_name = 'person_nik_per_institution'");
        if (empty($compositeIndex)) {
            Schema::table('core_persons', function (Blueprint $table) {
                $table->unique(['nik', 'institution_id'], 'person_nik_per_institution');
            });
        }
    }

    public function down(): void
    {
        Schema::table('core_persons', function (Blueprint $table) {
            $table->dropUnique('person_nik_per_institution');
        });

        $fkExists = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_NAME = 'core_persons' AND CONSTRAINT_NAME = 'core_persons_institution_id_foreign' AND TABLE_SCHEMA = DATABASE()");
        if (!empty($fkExists)) {
            Schema::table('core_persons', function (Blueprint $table) {
                $table->dropForeign('core_persons_institution_id_foreign');
            });
        }

        DB::statement('ALTER TABLE `core_persons` MODIFY `institution_id` CHAR(36) NULL');

        Schema::table('core_persons', function (Blueprint $table) {
            $table->foreign('institution_id')->references('id')->on('core_institutions')->cascadeOnDelete();
            $table->unique('nik', 'persons_nik_unique');
        });
    }
};
