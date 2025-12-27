<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sponsorship_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sponsorship_program_id')->constrained()->cascadeOnDelete();
            $table->string('beneficiary_type')->default('individual'); // individual, student, group, organization
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();
            $table->string('beneficiary_name');
            $table->string('beneficiary_email')->nullable();
            $table->string('beneficiary_phone')->nullable();
            $table->text('beneficiary_description')->nullable();
            $table->json('beneficiary_details')->nullable();
            $table->timestamps();

            $table->index('beneficiary_type');
            $table->index(['sponsorship_program_id']);
            $table->index(['student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sponsorship_beneficiaries');
    }
};
