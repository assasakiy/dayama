<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('core_persons', function (Blueprint $table) {
            // Drop composite unique lama jika ada
            $table->dropUnique('person_nik_per_institution');

            // Drop foreign key institution
            $table->dropForeign(['institution_id']);

            // Drop kolom institution_id
            $table->dropColumn('institution_id');

            // Jadikan NIK global unique (nullable)
            $table->unique('nik', 'core_persons_nik_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('core_persons', function (Blueprint $table) {
            $table->dropUnique('core_persons_nik_unique');

            $table->foreignUuid('institution_id')->nullable()->constrained('core_institutions')->cascadeOnDelete();

            $table->unique(['nik', 'institution_id'], 'person_nik_per_institution');
        });
    }
};
