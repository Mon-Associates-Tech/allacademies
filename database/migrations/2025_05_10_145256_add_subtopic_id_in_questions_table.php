<?php

use App\Models\AcademicSubtopic;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->foreignIdFor(AcademicSubtopic::class)->nullable();
        });

        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->foreignIdFor(AcademicSubtopic::class)->nullable();
        });

        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->foreignIdFor(AcademicSubtopic::class)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('essay_questions', function (Blueprint $table) {
            $table->dropForeign('subtopic_id');
        });

        Schema::table('multiple_choice_questions', function (Blueprint $table) {
            $table->dropForeign('subtopic_id');
        });

        Schema::table('true_or_false_questions', function (Blueprint $table) {
            $table->dropForeign('subtopic_id');
        });
    }
};
