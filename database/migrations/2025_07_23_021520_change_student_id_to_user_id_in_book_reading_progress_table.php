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
            $table->dropConstrainedForeignIdFor(Student::class);
            $table->foreignIdFor(User::class)->after('book_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_reading_progress', static function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(User::class);
            $table->foreignIdFor(Student::class)->after('book_id');
        });
    }
};
