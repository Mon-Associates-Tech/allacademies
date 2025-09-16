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
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'country')) {
                $table->string('country', 100)->default('Ghana')->after('address');
            }
            if (!Schema::hasColumn('schools', 'state')) {
                $table->string('state', 100)->nullable()->after('country');
            }
            if (!Schema::hasColumn('schools', 'city')) {
                $table->string('city', 100)->nullable()->after('state');
            }
            if (!Schema::hasColumn('schools', 'type')) {
                $table->enum('type', ['primary', 'secondary', 'tertiary', 'mixed', 'other'])->default('mixed')->after('description');
            }
            if (!Schema::hasColumn('schools', 'ownership')) {
                $table->enum('ownership', ['public', 'private', 'mission'])->default('public')->after('type');
            }
            if (!Schema::hasColumn('schools', 'student_capacity')) {
                $table->integer('student_capacity')->nullable()->after('ownership');
            }
            if (!Schema::hasColumn('schools', 'established_date')) {
                $table->date('established_date')->nullable()->after('student_capacity');
            }
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $columns = ['country', 'state', 'city', 'type', 'ownership', 'student_capacity', 'established_date'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('schools', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
