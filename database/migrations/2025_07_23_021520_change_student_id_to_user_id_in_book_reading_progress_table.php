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
                $table->dropUnique(['student_id']);
                $table->dropConstrainedForeignIdFor(Student::class);
            }
            if (!Schema::hasColumn('book_reading_progress', 'user_id')) {
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
                $table->dropConstrainedForeignIdFor(User::class);
            }
            if (!Schema::hasColumn('book_reading_progress', 'student_id')) {
                $table->foreignIdFor(Student::class)->after('book_id');
            }
        });
    }
};
