<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Change the payload column from LONGTEXT to LONGBLOB to store gzip binary data.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE general_exam_packages MODIFY COLUMN payload LONGBLOB");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE general_exam_packages MODIFY COLUMN payload LONGTEXT");
    }
};
