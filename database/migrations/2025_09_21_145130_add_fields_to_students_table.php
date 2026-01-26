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
        Schema::table('students', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable()->after('status');
            $table->string('blood_group')->nullable()->after('date_of_birth');
            $table->text('address')->nullable()->after('blood_group');
            $table->string('phone')->nullable()->after('address');
            $table->string('parent_name')->nullable()->after('phone');
            $table->string('parent_phone')->nullable()->after('parent_name');
            $table->string('parent_email')->nullable()->after('parent_phone');
            $table->string('emergency_contact')->nullable()->after('parent_email');
            $table->date('id_card_issue_date')->nullable()->after('emergency_contact');
            $table->date('id_card_expiry_date')->nullable()->after('id_card_issue_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth',
                'blood_group',
                'address',
                'phone',
                'parent_name',
                'parent_phone',
                'parent_email',
                'emergency_contact',
                'id_card_issue_date',
                'id_card_expiry_date',
            ]);
        });
    }
};
