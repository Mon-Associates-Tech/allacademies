<?php

use App\Models\Student;
use App\Models\User;
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
        Schema::table('book_reading_progress', static function (Blueprint $table) {
            if (Schema::hasColumn('book_reading_progress', 'student_id')) {
                // Drop foreign key first (before dropping unique constraint or column)
                try {
                    $table->dropForeign(['student_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist or have different name
                }

                // Drop unique constraint
                try {
                    $table->dropUnique(['student_id']);
                } catch (\Exception $e) {
                    // Unique constraint might not exist or have different name
                }

                // Drop the column
                $table->dropColumn('student_id');
            }

            if (! Schema::hasColumn('book_reading_progress', 'user_id')) {
                $table->foreignIdFor(User::class)->after('book_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reading_progress', static function (Blueprint $table) {
            if (Schema::hasColumn('book_reading_progress', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
                $table->dropColumn('user_id');
            }

            if (! Schema::hasColumn('book_reading_progress', 'student_id')) {
                $table->foreignIdFor(Student::class)->after('book_id');
                $table->unique('student_id');
            }
        });
    }
};
