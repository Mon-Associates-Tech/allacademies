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
        Schema::table('users', function (Blueprint $table) {
            // Add school_id for multi-tenancy (nullable for superadmin)
            $table->unsignedBigInteger('school_id')->nullable()->after('id');

            // Update existing fields
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('is_active');

            // Add foreign key
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');

            // Add indexes for performance
            $table->index(['school_id', 'email']);
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'role']);
        });

        Schema::table('students', function (Blueprint $table) {
            // Add student_id for school-specific identification
            $table->string('student_id', 50)->after('user_id');

            // Add admission tracking
            $table->date('admission_date')->nullable()->after('academic_group_id');
            $table->date('graduation_date')->nullable()->after('admission_date');
            $table->enum('status', ['active', 'inactive', 'graduated', 'transferred'])->default('active')->after('graduation_date');

            // Add metadata for additional student info
            $table->json('metadata')->nullable()->after('status');

            // Add unique constraints and indexes
            $table->unique(['school_id', 'student_id'], 'unique_school_student_id');
            $table->index(['school_id', 'status', 'created_at']);
            $table->index(['school_id', 'academic_level_id']);
            $table->index(['school_id', 'admission_date']);
        });

        Schema::table('teachers', function (Blueprint $table) {
            // Add school_id if not exists (from your existing School relationship)
            if (! Schema::hasColumn('teachers', 'school_id')) {
                $table->unsignedBigInteger('school_id')->after('id')->nullable();
            }

            // Add teacher-specific fields
            $table->string('employee_id', 50)->after('user_id');
            $table->string('department', 100)->nullable()->after('employee_id');
            $table->date('hire_date')->nullable()->after('department');
            $table->date('termination_date')->nullable()->after('hire_date');
            $table->decimal('salary', 10, 2)->nullable()->after('termination_date');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract'])->default('full_time')->after('salary');
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active')->after('employment_type');
            $table->json('qualifications')->nullable()->after('status');

            // Add unique constraints and indexes
            $table->unique(['school_id', 'employee_id'], 'unique_school_employee_id');
            $table->index(['school_id', 'status']);
            $table->index(['school_id', 'department']);
            $table->index(['school_id', 'hire_date']);
        });

        Schema::table('parents', function (Blueprint $table) {
            // Add school_id (parents can be associated with multiple schools through children)
            $table->unsignedBigInteger('school_id')->nullable()->after('id');

            // Add additional parent fields
            $table->string('relationship', 100)->nullable()->after('user_id');
            $table->string('occupation', 100)->nullable()->after('relationship');
            $table->string('phone', 20)->nullable()->after('occupation');
            $table->string('emergency_contact', 20)->nullable()->after('phone');
            $table->text('address')->nullable()->after('emergency_contact');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('address');

            // Add foreign key
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');

            // Add indexes
            $table->index(['school_id', 'status']);
            $table->index(['user_id', 'school_id']);
        });

        Schema::table('authors', function (Blueprint $table) {
            // Authors might not belong to specific schools (they can be global)
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('pen_name');

            // Add foreign keys
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');

            // Add indexes
            $table->index(['school_id', 'status']);
            $table->index('user_id');
        });

        // Update Librarians table
        Schema::table('librarians', function (Blueprint $table) {
            // Librarians belong to specific schools
            $table->string('employee_id', 50)->after('user_id');
            $table->date('hire_date')->nullable()->after('employee_id');
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active')->after('hire_date');

            // Add foreign keys
            $table->foreignId('school_id')->nullable()->references('id')->on('schools')->onDelete('cascade');

            // Add unique constraint and indexes
            $table->unique(['school_id', 'employee_id'], 'unique_school_librarian_id');
            $table->index(['school_id', 'status']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropIndex(['school_id', 'email']);
            $table->dropIndex(['school_id', 'status']);
            $table->dropIndex(['school_id', 'role']);
            $table->dropColumn(['school_id', 'phone', 'status']);
        });

        Schema::dropIfExists('user_system_roles');

        Schema::table('academic_groups', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropIndex(['school_id', 'name']);
            $table->dropIndex(['school_id', 'tag']);
            $table->dropUnique('unique_school_academic_group');
            if (! Schema::hasColumn('academic_groups', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('academic_levels', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['academic_group_id']);
            $table->dropIndex(['school_id', 'name']);
            $table->dropIndex(['school_id', 'academic_group_id']);
            $table->dropUnique('unique_school_level');
            $table->dropColumn(['school_id', 'academic_group_id']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['academic_level_id']);
            $table->dropForeign(['academic_group_id']);
            $table->dropUnique('unique_school_student_id');
            $table->dropIndex(['school_id', 'status', 'created_at']);
            $table->dropIndex(['school_id', 'academic_level_id']);
            $table->dropIndex(['school_id', 'admission_date']);
            $table->dropColumn([
                'student_id', 'admission_date', 'graduation_date',
                'status', 'metadata',
            ]);
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique('unique_school_employee_id');
            $table->dropIndex(['school_id', 'status']);
            $table->dropIndex(['school_id', 'department']);
            $table->dropIndex(['school_id', 'hire_date']);

            $table->dropColumn([
                'employee_id', 'department', 'hire_date', 'termination_date',
                'salary', 'employment_type', 'status', 'qualifications',
            ]);

            if (Schema::hasColumn('teachers', 'school_id')) {
                $table->dropColumn('school_id');
            }
        });

        Schema::table('parents', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['school_id', 'status']);
            $table->dropIndex(['user_id', 'school_id']);
            $table->dropColumn([
                'school_id', 'occupation', 'phone', 'emergency_contact',
                'address', 'status',
            ]);
        });

        Schema::table('authors', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            $table->dropIndex(['school_id', 'status']);
            $table->dropIndex(['user_id']);
            $table->dropColumn(['school_id', 'status']);
        });

        Schema::table('librarians', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropForeign(['user_id']);
            $table->dropUnique('unique_school_librarian_id');
            $table->dropIndex(['school_id', 'status']);
            $table->dropColumn(['school_id', 'employee_id', 'hire_date', 'status']);
        });

    }
};
