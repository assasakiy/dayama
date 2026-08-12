<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('core_person_positions');

        Schema::create('core_person_positions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('person_id')->constrained('core_persons')->cascadeOnDelete();
            $table->foreignUuid('position_id')->constrained('hr_positions')->cascadeOnDelete();
            $table->foreignUuid('institution_id')->nullable()->constrained('core_institutions')->nullOnDelete();
            $table->string('nomor_induk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->unique(['person_id', 'position_id', 'institution_id', 'tanggal_mulai'], 'person_position_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_person_positions');

        Schema::create('core_person_positions', function (Blueprint $table) {
            $table->foreignUuid('person_id')->constrained('core_persons')->cascadeOnDelete();
            $table->foreignUuid('position_id')->constrained('hr_positions')->cascadeOnDelete();
            $table->foreignUuid('institution_id')->nullable()->constrained('core_institutions')->nullOnDelete();
            $table->string('nomor_induk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->primary(['person_id', 'position_id']);
        });
    }
};
