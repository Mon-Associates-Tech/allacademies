<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_configurations', function (Blueprint $table): void {
            if (! Schema::hasColumn('report_card_configurations', 'principal_name')) {
                $table->string('principal_name')->nullable()->after('preparation_mode');
            }
            if (! Schema::hasColumn('report_card_configurations', 'principal_signature_path')) {
                $table->string('principal_signature_path')->nullable()->after('principal_name');
            }
            if (! Schema::hasColumn('report_card_configurations', 'class_teacher_name')) {
                $table->string('class_teacher_name')->nullable()->after('principal_signature_path');
            }
            if (! Schema::hasColumn('report_card_configurations', 'class_teacher_signature_path')) {
                $table->string('class_teacher_signature_path')->nullable()->after('class_teacher_name');
            }
        });

        Schema::table('score_weightings', function (Blueprint $table): void {
            if (! Schema::hasColumn('score_weightings', 'score_key')) {
                $table->string('score_key')->nullable()->after('name');
            }
            if (! Schema::hasColumn('score_weightings', 'source_type')) {
                $table->string('source_type')->nullable()->after('weight_percentage');
            }
            if (! Schema::hasColumn('score_weightings', 'max_score')) {
                $table->decimal('max_score', 8, 2)->nullable()->after('source_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('score_weightings', function (Blueprint $table): void {
            $dropColumns = [];
            if (Schema::hasColumn('score_weightings', 'score_key')) {
                $dropColumns[] = 'score_key';
            }
            if (Schema::hasColumn('score_weightings', 'source_type')) {
                $dropColumns[] = 'source_type';
            }
            if (Schema::hasColumn('score_weightings', 'max_score')) {
                $dropColumns[] = 'max_score';
            }
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });

        Schema::table('report_card_configurations', function (Blueprint $table): void {
            $dropColumns = [];
            if (Schema::hasColumn('report_card_configurations', 'principal_name')) {
                $dropColumns[] = 'principal_name';
            }
            if (Schema::hasColumn('report_card_configurations', 'principal_signature_path')) {
                $dropColumns[] = 'principal_signature_path';
            }
            if (Schema::hasColumn('report_card_configurations', 'class_teacher_name')) {
                $dropColumns[] = 'class_teacher_name';
            }
            if (Schema::hasColumn('report_card_configurations', 'class_teacher_signature_path')) {
                $dropColumns[] = 'class_teacher_signature_path';
            }
            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
