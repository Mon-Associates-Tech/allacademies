<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // For MySQL, we need to use raw SQL to modify ENUM
        DB::statement("ALTER TABLE login_activities MODIFY COLUMN logout_type ENUM('manual', 'session_timeout', 'forced', 'browser_close', 'new_session', 'auto_closed') NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE login_activities MODIFY COLUMN logout_type ENUM('manual', 'session_timeout', 'forced', 'browser_close') NULL");
    }
};
