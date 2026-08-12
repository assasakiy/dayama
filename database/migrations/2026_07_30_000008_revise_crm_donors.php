<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_donors', function (Blueprint $table) {
            // Add institution_id
            $table->foreignUuid('institution_id')
                ->nullable()
                ->after('person_id')
                ->constrained('core_institutions')
                ->nullOnDelete();

            // Replace donor_type with jenis_donatur
            $table->string('jenis_donatur', 30)
                ->nullable()
                ->after('institution_id');

            // Remove columns not in target schema
            $table->dropColumn(['donor_type', 'is_anonymous', 'notes', 'deleted_at']);
        });
    }

    public function down(): void
    {
        Schema::table('crm_donors', function (Blueprint $table) {
            $table->dropForeign(['institution_id']);
            $table->dropColumn(['institution_id', 'jenis_donatur']);
            $table->string('donor_type', 255)->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->text('notes')->nullable();
            $table->softDeletes();
        });
    }
};
