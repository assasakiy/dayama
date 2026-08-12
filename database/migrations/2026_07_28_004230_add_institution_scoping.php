<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add institution_id to core_persons
        Schema::table('core_persons', function (Blueprint $table) {
            $table->foreignUuid('institution_id')
                ->nullable()
                ->after('id')
                ->constrained('core_institutions')
                ->cascadeOnDelete();
        });

        // 2. Central NIK index — yayasan level, read-only for admin yayasan
        Schema::create('yayasan_person_index', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('nama_lengkap');
            $table->date('tanggal_lahir')->nullable();
            $table->json('refs');
            $table->timestamps();
        });

        // 3. Transfer audit log
        Schema::create('person_transfer_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('from_institution_id')->constrained('core_institutions');
            $table->foreignUuid('to_institution_id')->constrained('core_institutions');
            $table->foreignUuid('source_person_id')->constrained('core_persons');
            $table->foreignUuid('destination_person_id')->constrained('core_persons');
            $table->string('nik', 20)->nullable();
            $table->foreignUuid('triggered_by')->constrained('core_users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('person_transfer_logs');
        Schema::dropIfExists('yayasan_person_index');

        Schema::table('core_persons', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn('institution_id');
        });
    }
};
