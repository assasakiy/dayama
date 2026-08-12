<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('crm_guardians')) {
            Schema::create('crm_guardians', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('person_id');
                $table->uuid('student_id')->nullable();
                $table->uuid('relationship_type_id')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->boolean('is_emergency_contact')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('person_id')->references('id')->on('core_persons')->cascadeOnDelete();
                $table->foreign('student_id')->references('id')->on('academic_students')->cascadeOnDelete();
                $table->foreign('relationship_type_id')->references('id')->on('core_relationship_types')->nullOnDelete();

                $table->index('person_id');
                $table->index('student_id');
            });
        }

        if (!Schema::hasTable('crm_donors')) {
            Schema::create('crm_donors', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('person_id');
                $table->string('donor_type')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('person_id')->references('id')->on('core_persons')->cascadeOnDelete();

                $table->index('person_id');
            });
        }

        if (!Schema::hasTable('crm_partners')) {
            Schema::create('crm_partners', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('type')->nullable();
                $table->string('contact_person')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->string('website')->nullable();
                $table->string('logo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('slug');
            });
        }

        if (!Schema::hasTable('crm_subscribers')) {
            Schema::create('crm_subscribers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('email')->unique();
                $table->string('name')->nullable();
                $table->string('phone')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('subscribed_at');
                $table->timestamp('unsubscribed_at')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('email');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_subscribers');
        Schema::dropIfExists('crm_partners');
        Schema::dropIfExists('crm_donors');
        Schema::dropIfExists('crm_guardians');
    }
};