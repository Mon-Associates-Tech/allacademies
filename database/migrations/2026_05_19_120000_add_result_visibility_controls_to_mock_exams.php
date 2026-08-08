<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            // Allow administrators to retract/hide results after release
            $table->boolean('results_hidden_after_release')->default(false)
                  ->after('results_released_at')
                  ->comment('If true, hide results from participants even after release');
            
            // Control whether participants can see detailed question-by-question breakdown
            $table->boolean('show_question_breakdown')->default(true)
                  ->after('results_hidden_after_release')
                  ->comment('If true, show individual questions and answers to participants');
            
            // Control whether participants can see correct answers
            $table->boolean('show_correct_answers')->default(true)
                  ->after('show_question_breakdown')
                  ->comment('If true, show correct answers to participants');
            
            // Control whether participants can see their responses
            $table->boolean('show_participant_responses')->default(true)
                  ->after('show_correct_answers')
                  ->comment('If true, show participant\'s own responses');
        });
    }

    public function down(): void
    {
        Schema::table('mock_exams', function (Blueprint $table) {
            $table->dropColumn([
                'results_hidden_after_release',
                'show_question_breakdown',
                'show_correct_answers',
                'show_participant_responses',
            ]);
        });
    }
};
