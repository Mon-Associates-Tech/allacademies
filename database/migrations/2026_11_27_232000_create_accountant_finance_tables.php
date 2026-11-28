<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('category', 100);
            $table->string('description');
            $table->string('reference')->nullable();
            $table->decimal('amount', 15, 2);
            $table->char('currency', 3)->default('GHS');
            $table->date('expense_date');
            $table->string('status')->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['school_id', 'status', 'expense_date']);
        });

        Schema::create('financial_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source_type', 30);
            $table->unsignedBigInteger('source_id');
            $table->string('bank_reference')->nullable();
            $table->date('statement_date');
            $table->decimal('statement_amount', 15, 2);
            $table->char('statement_currency', 3);
            $table->text('note')->nullable();
            $table->timestamp('reconciled_at');
            $table->timestamps();

            $table->unique(['school_id', 'source_type', 'source_id'], 'financial_reconciliations_source_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reconciliations');
        Schema::dropIfExists('school_expenses');
    }
};
