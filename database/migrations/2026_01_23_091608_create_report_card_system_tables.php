<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add columns to existing grade_scales table
        Schema::table('grade_scales', function (Blueprint $table) {
            if (!Schema::hasColumn('grade_scales', 'academic_level_id')) {
                $table->foreignId('academic_level_id')->nullable()->after('school_id')->constrained()->cascadeOnDelete();
            }
            if (!Schema::hasColumn('grade_scales', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
            
            if (!Schema::hasIndex('grade_scales', 'grade_scales_school_id_academic_level_id_index')) {
                $table->index(['school_id', 'academic_level_id']);
            }
        });

        // Score Weightings Configuration
        Schema::create('score_weightings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "Class Score", "Tests", "Exams"
            $table->decimal('weight_percentage', 5, 2);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
            
            $table->index(['school_id', 'academic_level_id', 'academic_subject_id'], 'score_weightings_composite_idx');
        });

        // Report Card Templates
        Schema::create('report_card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('custom_columns')->nullable(); // Additional columns beyond defaults
            $table->json('header_config')->nullable(); // Letterhead customization
            $table->json('footer_config')->nullable(); // Signature fields, etc.
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        // Report Card Configuration (per academic period & level)
        Schema::create('report_card_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('academic_level_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_card_template_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_published')->default(false);
            $table->datetime('available_from')->nullable();
            $table->datetime('available_until')->nullable();
            $table->enum('preparation_mode', ['manual', 'automated', 'hybrid'])->default('hybrid');
            $table->timestamps();
            
            $table->unique(['academic_period_id', 'academic_level_id'], 'rc_config_period_level_unique');
        });

        // Enhanced Report Cards table
        Schema::table('report_cards', function (Blueprint $table) {
            if (!Schema::hasColumn('report_cards', 'report_card_configuration_id')) {
                $table->foreignId('report_card_configuration_id')->nullable()->after('school_id')->constrained()->nullOnDelete();
            }
            if (!Schema::hasColumn('report_cards', 'status')) {
                $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'published'])->default('draft')->after('generated_at');
            }
            if (!Schema::hasColumn('report_cards', 'submitted_at')) {
                $table->datetime('submitted_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('report_cards', 'approved_at')) {
                $table->datetime('approved_at')->nullable()->after('submitted_at');
            }
            if (!Schema::hasColumn('report_cards', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('report_cards', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('report_cards', 'is_accessible')) {
                $table->boolean('is_accessible')->default(false)->after('rejection_reason');
            }
        });

        // Enhanced Report Card Grades table
        Schema::table('report_card_grades', function (Blueprint $table) {
            if (!Schema::hasColumn('report_card_grades', 'scores')) {
                $table->json('scores')->nullable()->after('subject_id'); // Flexible score columns
            }
            if (!Schema::hasColumn('report_card_grades', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('remarks');
            }
            if (!Schema::hasColumn('report_card_grades', 'last_modified_by')) {
                $table->foreignId('last_modified_by')->nullable()->after('is_locked')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('report_card_grades', 'last_modified_at')) {
                $table->datetime('last_modified_at')->nullable()->after('last_modified_by');
            }
        });

        // Report Card Access Revocations
        Schema::create('report_card_revocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_card_configuration_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('academic_subject_id')->nullable()->constrained()->cascadeOnDelete();
            $table->enum('revocation_type', ['level', 'student', 'subject']);
            $table->text('reason')->nullable();
            $table->foreignId('revoked_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            
            $table->index(['report_card_configuration_id', 'student_id'], 'rc_revocations_config_student_idx');
        });

        // Report Card Change Log
        Schema::create('report_card_change_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('report_card_grade_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action'); // 'created', 'updated', 'submitted', 'approved', 'rejected'
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['report_card_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_change_logs');
        Schema::dropIfExists('report_card_revocations');
        
        Schema::table('report_card_grades', function (Blueprint $table) {
            $table->dropColumn(['scores', 'is_locked', 'last_modified_by', 'last_modified_at']);
        });
        
        Schema::table('report_cards', function (Blueprint $table) {
            $table->dropColumn(['report_card_configuration_id', 'status', 'submitted_at', 'approved_at', 'approved_by', 'rejection_reason', 'is_accessible']);
        });
        
        Schema::dropIfExists('report_card_configurations');
        Schema::dropIfExists('report_card_templates');
        Schema::dropIfExists('score_weightings');
        
        Schema::table('grade_scales', function (Blueprint $table) {
            $table->dropForeign(['academic_level_id']);
            $table->dropIndex(['school_id', 'academic_level_id']);
            $table->dropColumn(['academic_level_id', 'is_default']);
        });
    }
};
