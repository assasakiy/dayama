<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hr_positions', function (Blueprint $table) {
            $table->string('jenis_jabatan', 30)
                ->default('fungsional_pendidikan')
                ->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('hr_positions', function (Blueprint $table) {
            $table->dropColumn('jenis_jabatan');
        });
    }
};
