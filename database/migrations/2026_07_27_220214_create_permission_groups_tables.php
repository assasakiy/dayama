<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('core_permission_groups', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('core_permission_group_permission', function (Blueprint $table) {
            $table->uuid('permission_group_id');
            $table->uuid('permission_id');
            $table->primary(['permission_group_id', 'permission_id']);
            $table->foreign('permission_group_id')->references('id')->on('core_permission_groups')->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('core_permissions')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('core_permission_group_permission');
        Schema::dropIfExists('core_permission_groups');
    }
};
