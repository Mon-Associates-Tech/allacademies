<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_aids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            // Make code unique per school context logic handled in application or composite index if preferred
            $table->string('code');
            $table->text('description')->nullable();
            $table->decimal('amount', 10, 2)->default(0);

            $table->foreignId('school_payment_structure_id')
                ->nullable()
                ->constrained('school_payment_structures')
                ->nullOnDelete();

            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Ensure code is unique per school
            $table->unique(['school_id', 'code']);
        });

        Schema::create('financial_aid_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_aid_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['financial_aid_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_aid_student');
        Schema::dropIfExists('financial_aids');
    }
};
