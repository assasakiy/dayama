<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->uuid('parent_id')->nullable();
            $table->uuid('institution_type_id')->nullable();
            $table->string('status', 30)->default('draft');
            $table->string('kode', 100)->nullable()->unique();
            $table->text('alamat')->nullable();
            $table->uuid('created_by')->nullable();
            $table->uuid('completed_by')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->foreign('parent_id')->references('id')->on('institutions')->nullOnDelete();
            $table->foreign('institution_type_id')->references('id')->on('institution_types')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('completed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropForeign(['institution_type_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['completed_by']);

            $table->dropColumn([
                'parent_id', 'institution_type_id', 'status', 'kode',
                'alamat', 'created_by', 'completed_by', 'completed_at', 'verified_at',
            ]);
        });
    }
};
