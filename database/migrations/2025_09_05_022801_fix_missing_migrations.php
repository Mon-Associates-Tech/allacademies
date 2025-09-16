<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\School;

return new class extends Migration
{
    public function up(): void
    {
        // First, create the pivot tables for school assignments
        $this->createSchoolAssignmentTables();

        // Create a default school if none exists
        $defaultSchool = School::first();
        if (!$defaultSchool) {
            $defaultSchool = School::create([
                'name' => 'Default School',
                'code' => 'DEF001',
                'email' => 'admin@defaultschool.edu',
                'status' => 'active',
                'subscription_plan' => 'standard'
            ]);
        } else {
            // Update existing school with required fields
            $defaultSchool->update([
                'code' => $defaultSchool->code ?? 'SCH001',
                'status' => 'active'
            ]);
        }

        $schoolId = $defaultSchool->id;

        // Migrate existing academic groups to school assignments
        $this->migrateAcademicGroupAssignments($schoolId);

        // Migrate existing academic levels to school assignments
        $this->migrateAcademicLevelAssignments($schoolId);

        // Update users table (excluding superadmin and owner roles)
        DB::table('users')
            ->whereNull('school_id')
            ->whereNotIn('role', ['superadmin', 'owner'])
            ->update(['school_id' => $schoolId]);

        // Update students and generate student IDs
        $this->updateStudentsWithSchool($schoolId);

        // Update teachers and generate employee IDs
        $this->updateTeachersWithSchool($schoolId);

        // Update librarians and generate employee IDs
        $this->updateLibrariansWithSchool($schoolId);

        // Update parents
        DB::table('parents')
            ->whereNull('school_id')
            ->update([
                'school_id' => $schoolId,
                'status' => 'active'
            ]);
    }

    private function createSchoolAssignmentTables(): void
    {
        // Create school_academic_groups pivot table
        if (!Schema::hasTable('school_academic_group')) {
            Schema::create('school_academic_group', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->foreignId('academic_group_id')->constrained('academic_groups')->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->json('custom_settings')->nullable(); // For school-specific customizations
                $table->timestamps();

                $table->unique(['school_id', 'academic_group_id']);
                $table->index(['school_id', 'is_active']);
            });
        }

        // Create school_academic_levels pivot table
        if (!Schema::hasTable('school_academic_level')) {
            Schema::create('school_academic_level', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                $table->foreignId('academic_level_id')->constrained('academic_levels')->onDelete('cascade');
                $table->foreignId('academic_group_id')->constrained('academic_groups')->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->integer('sort_order')->default(0);
                $table->json('custom_settings')->nullable(); // For school-specific customizations
                $table->timestamps();

                $table->unique(['school_id', 'academic_level_id']);
                $table->index(['school_id', 'academic_group_id', 'is_active'], 'school_academic_levels_index');
            });
        }
    }

    private function migrateAcademicGroupAssignments($schoolId): void
    {
        // Get all existing academic groups that might have school_id
        $existingGroups = DB::table('academic_groups')->get();

        foreach ($existingGroups as $group) {
            // Check if this school-group assignment already exists
            $exists = DB::table('school_academic_group')
                ->where('school_id', $schoolId)
                ->where('academic_group_id', $group->id)
                ->exists();

            if (!$exists) {
                DB::table('school_academic_group')->insert([
                    'school_id' => $schoolId,
                    'academic_group_id' => $group->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Remove school_id column from academic_groups if it exists
        if (Schema::hasColumn('academic_groups', 'school_id')) {
            Schema::table('academic_groups', function (Blueprint $table) {
                // Check if foreign key exists before dropping
                if ($this->foreignKeyExists('academic_groups', 'academic_groups_school_id_foreign')) {
                  //  $table->dropForeign(['school_id']);
                }
              //  $table->dropColumn('school_id');
            });
        }
    }

    private function migrateAcademicLevelAssignments($schoolId): void
    {
        // Get all existing academic levels
        $existingLevels = DB::table('academic_levels')->get();

        foreach ($existingLevels as $level) {
            // Find the corresponding school_academic_group_id
            $schoolAcademicGroup = DB::table('school_academic_group')
                ->where('school_id', $schoolId)
                ->where('academic_group_id', $level->academic_group_id)
                ->first();

            if ($schoolAcademicGroup) {
                // Check if this school-level assignment already exists
                $exists = DB::table('school_academic_level')
                    ->where('school_id', $schoolId)
                    ->where('academic_level_id', $level->id)
                    ->exists();

                if (!$exists) {
                    DB::table('school_academic_level')->insert([
                        'school_id' => $schoolId,
                        'academic_level_id' => $level->id,
                        'academic_group_id' => $level->academic_group_id, // Use the original academic_group_id
                        'is_active' => true,
                        'sort_order' => $level->sort_order ?? 0,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
        }


        // Remove school_id column from academic_levels if it exists
        if (Schema::hasColumn('academic_levels', 'school_id')) {
            // Drop foreign key if it exists
            if ($this->foreignKeyExists('academic_levels', 'academic_levels_school_id_foreign')) {
                Schema::table('academic_levels', function (Blueprint $table) {
                   // $table->dropForeign(['school_id']);
                });
            }

            // Drop the column using raw SQL to avoid Laravel schema issues
           // DB::statement('ALTER TABLE academic_levels DROP COLUMN school_id');
        }
    }

    private function updateStudentsWithSchool($schoolId): void
    {


        // Generate student IDs for existing students
        $students = DB::table('students')->whereNull('student_id')->get();
        foreach ($students as $student) {
            $school = School::find($student->school_id);
            $year = date('Y');
            $sequence = DB::table('students')
                    ->where('school_id', $student->school_id)
                    ->where('student_id', 'like', "{$school->code}{$year}%")
                    ->count() + 1;

            $studentId = $school->code . $year . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            DB::table('students')
                ->where('id', $student->id)
                ->update([
                    'student_id' => $studentId,
                    'admission_date' => $student->created_at ?? now(),
                    'status' => 'active'
                ]);
        }
    }

    private function updateTeachersWithSchool($schoolId): void
    {
        // Generate employee IDs for existing teachers
        $teachers = DB::table('teachers')->whereNull('employee_id')->get();
        foreach ($teachers as $teacher) {
            $school = School::find($teacher->school_id);
            $sequence = DB::table('teachers')
                    ->where('school_id', $teacher->school_id)
                    ->where('employee_id', 'like', "{$school->code}T%")
                    ->count() + 1;

            $employeeId = $school->code . 'T' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            DB::table('teachers')
                ->where('id', $teacher->id)
                ->update([
                    'employee_id' => $employeeId,
                    'hire_date' => $teacher->created_at ?? now(),
                    'employment_type' => 'full_time',
                    'status' => 'active'
                ]);
        }
    }

    private function updateLibrariansWithSchool($schoolId): void
    {
        // Generate employee IDs for librarians
        $librarians = DB::table('librarians')->whereNull('employee_id')->get();
        foreach ($librarians as $librarian) {
            $school = School::find($librarian->school_id);
            $sequence = DB::table('librarians')
                    ->where('school_id', $librarian->school_id)
                    ->where('employee_id', 'like', "{$school->code}L%")
                    ->count() + 1;

            $employeeId = $school->code . 'L' . str_pad($sequence, 4, '0', STR_PAD_LEFT);

            DB::table('librarians')
                ->where('id', $librarian->id)
                ->update([
                    'employee_id' => $employeeId,
                    'hire_date' => $librarian->created_at ?? now(),
                    'status' => 'active'
                ]);
        }
    }


    /**
     * Check if a foreign key constraint exists on a table
     */
    private function foreignKeyExists($table, $foreignKey): bool
    {
        $schema = Schema::getConnection()->getSchemaBuilder();
        $database = $schema->getConnection()->getDatabaseName();

        // For MySQL
        if ($schema->getConnection()->getDriverName() === 'mysql') {
            $result = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = ?
                AND TABLE_NAME = ?
                AND CONSTRAINT_NAME = ?
            ", [$database, $table, $foreignKey]);

            return !empty($result);
        }

        // For PostgreSQL
        if ($schema->getConnection()->getDriverName() === 'pgsql') {
            $result = DB::select("
                SELECT constraint_name
                FROM information_schema.table_constraints
                WHERE table_name = ?
                AND constraint_type = 'FOREIGN KEY'
                AND constraint_name = ?
            ", [$table, $foreignKey]);

            return !empty($result);
        }

        // For SQLite - check if column exists (SQLite handles FKs differently)
        if ($schema->getConnection()->getDriverName() === 'sqlite') {
            // In SQLite, we'll just check if the column exists since FKs are handled differently
            return Schema::hasColumn($table, 'school_id');
        }

        // Default: assume it exists to avoid errors
        return true;
    }
    public function down(): void
    {
        // Drop the pivot tables
        Schema::dropIfExists('school_academic_level');
        Schema::dropIfExists('school_academic_group');

        // Note: Be careful with rollback as this could be destructive
        // You might want to back up data before running this migration
    }
};
