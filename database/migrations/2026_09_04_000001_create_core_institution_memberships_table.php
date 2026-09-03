<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('core_institution_memberships')) {
            Schema::create('core_institution_memberships', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->foreignUuid('person_id')->constrained('core_persons')->cascadeOnDelete();
                $table->foreignUuid('institution_id')->constrained('core_institutions')->cascadeOnDelete();
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->date('joined_at')->nullable();
                $table->date('left_at')->nullable();
                $table->timestamps();

                $table->unique(['person_id', 'institution_id'], 'uq_person_institution');
            });
        }

        // Backfill data aman: jika core_persons memiliki baris dengan institution_id
        if (Schema::hasColumn('core_persons', 'institution_id')) {
            $persons = DB::table('core_persons')
                ->whereNotNull('institution_id')
                ->select(['id', 'institution_id', 'created_at'])
                ->get();

            foreach ($persons as $person) {
                DB::table('core_institution_memberships')->insertOrIgnore([
                    'id'             => (string) Str::orderedUuid(),
                    'person_id'      => $person->id,
                    'institution_id' => $person->institution_id,
                    'status'         => 'active',
                    'joined_at'      => $person->created_at ? substr((string) $person->created_at, 0, 10) : now()->toDateString(),
                    'left_at'        => null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('core_institution_memberships');
    }
};
