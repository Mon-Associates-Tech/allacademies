<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new analytics fields to books table
        Schema::table('books', function (Blueprint $table) {
            $table->json('age_groups')->nullable()->after('status');
            $table->json('academic_group_ids')->nullable()->after('age_groups');
            $table->json('academic_level_ids')->nullable()->after('academic_group_ids');
            $table->json('academic_subject_ids')->nullable()->after('academic_level_ids');
        });

        // Make user_id nullable in authors table to allow standalone author records
        Schema::table('authors', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Cleanup virtual author users - only nullify user_id, don't delete users
        $virtualAuthors = DB::table('users')
            ->where('role', 'author')
            ->whereNotNull('password')
            ->whereRaw('LENGTH(name) - LENGTH(REPLACE(name, " ", "")) = 0') // Single word names only
            ->get();

        foreach ($virtualAuthors as $user) {
            // Check if password matches the default password
            if (Hash::check('defaultpassword123', $user->password)) {
                // Get the author record
                $author = DB::table('authors')->where('user_id', $user->id)->first();

                if ($author) {
                    // If author name is empty, use user name
                    if (empty($author->name)) {
                        DB::table('authors')
                            ->where('id', $author->id)
                            ->update(['name' => $user->name]);
                    }

                    // Nullify user_id in authors table (author record remains)
                    DB::table('authors')
                        ->where('id', $author->id)
                        ->update(['user_id' => null]);
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['age_groups', 'academic_group_ids', 'academic_level_ids', 'academic_subject_ids']);
        });

        // Note: We cannot reverse the user deletion or make user_id non-nullable
        // as that would require recreating deleted data
    }
};
