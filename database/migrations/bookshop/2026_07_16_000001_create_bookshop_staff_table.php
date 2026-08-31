<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bookshop_staff')) {
            Schema::create('bookshop_staff', function (Blueprint $table) {
                $table->id();
 
                // No ->constrained() yet — bookshop_branches doesn't exist until the
                // next migration, and branches references staff too (creator).
                // The FK constraint is added back in
                // 2026_07_16_000003_add_branch_foreign_key_to_bookshop_staff_table.
                $table->unsignedBigInteger('branch_id')->nullable();
 
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->string('role')->default('admin'); // StaffRole enum: superadmin|admin
                $table->boolean('is_active')->default(true);
                $table->boolean('must_change_password')->default(true);
                $table->timestamp('last_login_at')->nullable();
 
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
 
                $table->index('role');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookshop_staff');
    }
};
