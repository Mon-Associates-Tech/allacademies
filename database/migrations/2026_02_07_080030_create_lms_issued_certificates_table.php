<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('lms_issued_certificates')) {
            Schema::create('lms_issued_certificates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('template_id')->constrained('lms_certificate_templates')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('course_id')->nullable()->constrained('lms_courses')->nullOnDelete();
                $table->foreignId('enrollment_id')->nullable()->constrained('lms_enrollments')->nullOnDelete();
                $table->string('certificate_number')->unique();
                $table->string('recipient_name');
                $table->date('issue_date');
                $table->date('expiry_date')->nullable();
                $table->json('custom_data')->nullable(); // Course title, completion date, grade, etc.
                $table->string('verification_code')->unique();
                $table->string('pdf_path')->nullable();
                $table->timestamps();

                $table->index('user_id');
                $table->index('course_id');
                $table->index('issue_date');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_issued_certificates');
    }
};
