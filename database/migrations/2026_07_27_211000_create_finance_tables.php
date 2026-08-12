<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('finance_payment_types')) {
            Schema::create('finance_payment_types', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        if (!Schema::hasTable('finance_invoices')) {
            Schema::create('finance_invoices', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('invoice_number')->unique();
                $table->string('invoiceable_type');
                $table->uuid('invoiceable_id');
                $table->uuid('student_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->date('due_date')->nullable();
                $table->string('status')->default('pending');
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index('invoice_number');
                $table->index('status');
                $table->index('student_id');

                $table->foreign('student_id')->references('id')->on('academic_students')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('finance_payments')) {
            Schema::create('finance_payments', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('payment_type_id');
                $table->string('payable_type');
                $table->uuid('payable_id');
                $table->uuid('invoice_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->datetime('payment_date');
                $table->string('payment_method')->nullable();
                $table->string('reference_number')->nullable();
                $table->text('notes')->nullable();
                $table->uuid('paid_by')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->uuid('verified_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('payment_type_id')->references('id')->on('finance_payment_types');
                $table->foreign('invoice_id')->references('id')->on('finance_invoices')->nullOnDelete();
                $table->foreign('paid_by')->references('id')->on('core_users')->nullOnDelete();
                $table->foreign('verified_by')->references('id')->on('core_users')->nullOnDelete();

                $table->index('payment_type_id');
                $table->index('invoice_id');
                $table->index(['payable_type', 'payable_id']);
            });
        }

        if (!Schema::hasTable('finance_transactions')) {
            Schema::create('finance_transactions', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('from_account')->nullable();
                $table->string('to_account')->nullable();
                $table->decimal('amount', 15, 2);
                $table->string('type');
                $table->string('category')->nullable();
                $table->text('description')->nullable();
                $table->uuid('reference_id')->nullable();
                $table->string('reference_type')->nullable();
                $table->datetime('transaction_date');
                $table->uuid('created_by')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('created_by')->references('id')->on('core_users')->nullOnDelete();

                $table->index('type');
                $table->index('category');
                $table->index('transaction_date');
            });
        }

        if (!Schema::hasTable('finance_donations')) {
            Schema::create('finance_donations', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('donor_id')->nullable();
                $table->decimal('amount', 15, 2);
                $table->date('donation_date');
                $table->uuid('payment_type_id')->nullable();
                $table->string('campaign')->nullable();
                $table->boolean('is_anonymous')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('donor_id')->references('id')->on('crm_donors')->nullOnDelete();
                $table->foreign('payment_type_id')->references('id')->on('finance_payment_types')->nullOnDelete();

                $table->index('donor_id');
                $table->index('donation_date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_donations');
        Schema::dropIfExists('finance_transactions');
        Schema::dropIfExists('finance_payments');
        Schema::dropIfExists('finance_invoices');
        Schema::dropIfExists('finance_payment_types');
    }
};
