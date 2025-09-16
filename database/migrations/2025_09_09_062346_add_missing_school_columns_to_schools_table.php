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
            // Location fields
            if (!Schema::hasColumn('schools', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('schools', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('schools', 'country')) {
                $table->string('country', 100)->default('Ghana')->after('state'); // Default to Ghana based on your location
            }
            if (!Schema::hasColumn('schools', 'postal_code')) {
                $table->string('postal_code', 20)->nullable()->after('country');
            }

            // School details
            if (!Schema::hasColumn('schools', 'type')) {
                $table->enum('type', ['primary', 'secondary', 'tertiary', 'mixed'])->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('schools', 'student_capacity')) {
                $table->integer('student_capacity')->nullable()->unsigned()->after('type');
            }

            // System and configuration
            if (!Schema::hasColumn('schools', 'timezone')) {
                $table->string('timezone', 50)->default('Africa/Accra')->after('student_capacity');
            }
            if (!Schema::hasColumn('schools', 'currency')) {
                $table->char('currency', 3)->default('GHS')->after('timezone'); // Ghana Cedis
            }

            // Academic year dates
            if (!Schema::hasColumn('schools', 'academic_year_start')) {
                $table->date('academic_year_start')->nullable()->after('currency');
            }
            if (!Schema::hasColumn('schools', 'academic_year_end')) {
                $table->date('academic_year_end')->nullable()->after('academic_year_start');
            }

            // Add indexes for better performance
            if (!Schema::hasColumn('schools', 'country') || !Schema::hasColumn('schools', 'state') || !Schema::hasColumn('schools', 'city')) {
                $table->index(['country', 'state', 'city']);
            }
            if (!Schema::hasColumn('schools', 'type')) {
                $table->index('type');
            }
            if (!Schema::hasColumn('schools', 'academic_year_start') || !Schema::hasColumn('schools', 'academic_year_end')) {
                $table->index(['academic_year_start', 'academic_year_end']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $table->dropIndex(['country', 'state', 'city']);
            $table->dropIndex(['type']);
            $table->dropIndex(['academic_year_start', 'academic_year_end']);

            $table->dropColumn([
                'city',
                'state',
                'country',
                'postal_code',
                'type',
                'description',
                'timezone',
                'currency',
                'academic_year_start',
                'academic_year_end'
            ]);
        });
    }
};
