<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_person_positions', function (Blueprint $table) {
            $table->uuid('person_id');
            $table->uuid('position_id');
            $table->uuid('institution_id')->nullable();
            $table->string('nomor_induk')->nullable();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('person_id')->references('id')->on('core_persons')->cascadeOnDelete();
            $table->foreign('position_id')->references('id')->on('hr_positions')->cascadeOnDelete();
            $table->foreign('institution_id')->references('id')->on('core_institutions')->nullOnDelete();
            $table->primary(['person_id', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_person_positions');
    }
};
