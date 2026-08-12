<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('inventory_asset_categories')) {
            Schema::create('inventory_asset_categories', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->uuid('parent_id')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('parent_id')->references('id')->on('inventory_asset_categories')->onDelete('set null');
                $table->index('parent_id');
                $table->index('is_active');
                $table->index('sort_order');
            });
        }

        if (!Schema::hasTable('inventory_rooms')) {
            Schema::create('inventory_rooms', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('code')->nullable();
                $table->text('location')->nullable();
                $table->integer('capacity')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('inventory_items')) {
            Schema::create('inventory_items', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('sku')->nullable()->unique();
                $table->uuid('category_id')->nullable();
                $table->uuid('room_id')->nullable();
                $table->text('description')->nullable();
                $table->integer('quantity')->default(0);
                $table->integer('minimum_stock')->default(0);
                $table->string('unit')->nullable();
                $table->string('condition')->default('baik');
                $table->date('purchase_date')->nullable();
                $table->decimal('purchase_price', 15, 2)->nullable();
                $table->string('supplier')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('category_id')->references('id')->on('inventory_asset_categories')->onDelete('set null');
                $table->foreign('room_id')->references('id')->on('inventory_rooms')->onDelete('set null');
                $table->index('category_id');
                $table->index('room_id');
                $table->index('sku');
                $table->index('is_active');
            });
        }

        if (!Schema::hasTable('inventory_stocks')) {
            Schema::create('inventory_stocks', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('item_id');
                $table->string('type');
                $table->integer('quantity');
                $table->string('reference_type')->nullable();
                $table->uuid('reference_id')->nullable();
                $table->text('notes')->nullable();
                $table->uuid('recorded_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
                $table->foreign('recorded_by')->references('id')->on('core_users')->onDelete('set null');
                $table->index('item_id');
                $table->index('type');
            });
        }

        if (!Schema::hasTable('inventory_asset_movements')) {
            Schema::create('inventory_asset_movements', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('item_id');
                $table->uuid('from_room_id')->nullable();
                $table->uuid('to_room_id')->nullable();
                $table->integer('quantity');
                $table->dateTime('movement_date');
                $table->text('reason')->nullable();
                $table->text('notes')->nullable();
                $table->uuid('recorded_by')->nullable();
                $table->timestamps();

                $table->foreign('item_id')->references('id')->on('inventory_items')->onDelete('cascade');
                $table->foreign('from_room_id')->references('id')->on('inventory_rooms')->onDelete('set null');
                $table->foreign('to_room_id')->references('id')->on('inventory_rooms')->onDelete('set null');
                $table->foreign('recorded_by')->references('id')->on('core_users')->onDelete('set null');
                $table->index('item_id');
                $table->index('from_room_id');
                $table->index('to_room_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_asset_movements');
        Schema::dropIfExists('inventory_stocks');
        Schema::dropIfExists('inventory_items');
        Schema::dropIfExists('inventory_rooms');
        Schema::dropIfExists('inventory_asset_categories');
    }
};