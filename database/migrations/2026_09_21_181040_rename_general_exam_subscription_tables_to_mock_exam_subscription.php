<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create Mock Exam Subscription Plans
        if (!Schema::hasTable('mock_exam_subscription_plans')) {
            Schema::create('mock_exam_subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type');
                $table->unsignedInteger('max_subjects')->nullable();
                $table->unsignedInteger('max_exams')->nullable();
                $table->unsignedInteger('max_participants')->nullable();
                $table->string('duration_type')->nullable();
                $table->unsignedInteger('duration_value')->nullable();
                $table->decimal('base_price', 10, 2)->default(0.00);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. Create Mock Exam Subscriptions
        if (!Schema::hasTable('mock_exam_subscriptions')) {
            Schema::create('mock_exam_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->index();
                $table->foreignId('mock_exam_subscription_plan_id')->index();
                $table->string('type');
                $table->string('status');
                $table->unsignedInteger('participant_slots')->default(0);
                $table->unsignedInteger('participants_used')->default(0);
                $table->unsignedInteger('exams_used')->default(0);
                $table->unsignedInteger('max_exams')->default(0);
                $table->decimal('amount_paid', 10, 2)->default(0.00);
                $table->boolean('granted_by_owner')->default(false);
                $table->foreignId('granted_by')->nullable()->index();
                $table->timestamp('activated_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 3. Create Mock Exam Subscription Payments
        if (!Schema::hasTable('mock_exam_subscription_payments')) {
            Schema::create('mock_exam_subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mock_exam_subscription_id')->index();
                $table->foreignId('user_id')->index();
                $table->string('paystack_reference')->nullable();
                $table->string('paystack_access_code')->nullable();
                $table->decimal('amount', 10, 2)->default(0.00);
                $table->string('currency')->default('USD');
                $table->string('status')->default('pending');
                $table->string('payment_type')->nullable();
                $table->unsignedInteger('additional_participants')->default(0);
                $table->json('paystack_response')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }

        // 4. Create Mock Exam Subscription Subjects Pivot
        if (!Schema::hasTable('mock_exam_subscription_subjects')) {
            Schema::create('mock_exam_subscription_subjects', function (Blueprint $table) {
                $table->id();
                $table->foreignId('mock_exam_subscription_id')->index();
                $table->foreignId('academic_subject_id')->index();
                $table->timestamps();

                $table->unique(['mock_exam_subscription_id', 'academic_subject_id'], 'mock_sub_subject_unique');
            });
        }

        // =========================================================================
        // 5. COPY DATA FROM GENERAL TABLES TO MOCK TABLES
        // =========================================================================

        // Copy Plans
        if (Schema::hasTable('general_exam_subscription_plans')) {
            DB::table('mock_exam_subscription_plans')->insertUsing(
                ['id', 'name', 'type', 'max_subjects', 'max_exams', 'max_participants', 'duration_type', 'duration_value', 'base_price', 'is_active', 'description', 'created_at', 'updated_at'],
                function ($query) {
                    $query->select([
                        'id', 'name', 'type', 'max_subjects', 'max_exams', 'max_participants',
                        'duration_type', 'duration_value', 'base_price', 'is_active', 'description',
                        'created_at', 'updated_at'
                    ])->from('general_exam_subscription_plans');
                }
            );
        }

        // Copy Subscriptions (Notice the mapping of general_..._plan_id to mock_..._plan_id)
        if (Schema::hasTable('general_exam_subscriptions')) {
            DB::table('mock_exam_subscriptions')->insertUsing(
                [
                    'id', 'user_id', 'mock_exam_subscription_plan_id', 'type', 'status',
                    'participant_slots', 'participants_used', 'exams_used', 'max_exams',
                    'amount_paid', 'granted_by_owner', 'granted_by', 'activated_at',
                    'expires_at', 'created_at', 'updated_at', 'deleted_at'
                ],
                function ($query) {
                    $query->select([
                        'id', 'user_id', 'general_exam_subscription_plan_id', 'type', 'status',
                        'participant_slots', 'participants_used', 'exams_used', 'max_exams',
                        'amount_paid', 'granted_by_owner', 'granted_by', 'activated_at',
                        'expires_at', 'created_at', 'updated_at', 'deleted_at'
                    ])->from('general_exam_subscriptions');
                }
            );
        }

        // Copy Payments (Notice the mapping of general_..._id to mock_..._id)
        if (Schema::hasTable('general_exam_subscription_payments')) {
            DB::table('mock_exam_subscription_payments')->insertUsing(
                [
                    'id', 'mock_exam_subscription_id', 'user_id', 'paystack_reference',
                    'paystack_access_code', 'amount', 'currency', 'status', 'payment_type',
                    'additional_participants', 'paystack_response', 'paid_at', 'created_at', 'updated_at'
                ],
                function ($query) {
                    $query->select([
                        'id', 'general_exam_subscription_id', 'user_id', 'paystack_reference',
                        'paystack_access_code', 'amount', 'currency', 'status', 'payment_type',
                        'additional_participants', 'paystack_response', 'paid_at', 'created_at', 'updated_at'
                    ])->from('general_exam_subscription_payments');
                }
            );
        }

        // Copy Pivot Subjects (Notice the mapping of general_..._id to mock_..._id)
        if (Schema::hasTable('general_exam_subscription_subjects')) {
            DB::table('mock_exam_subscription_subjects')->insertUsing(
                ['id', 'mock_exam_subscription_id', 'academic_subject_id', 'created_at', 'updated_at'],
                function ($query) {
                    $query->select([
                        'id', 'general_exam_subscription_id', 'academic_subject_id', 'created_at', 'updated_at'
                    ])->from('general_exam_subscription_subjects');
                }
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Because we duplicated the tables, rolling back simply means dropping the new mock tables.
        // The original general_exam_* tables remain completely untouched.
        Schema::dropIfExists('mock_exam_subscription_subjects');
        Schema::dropIfExists('mock_exam_subscription_payments');
        Schema::dropIfExists('mock_exam_subscriptions');
        Schema::dropIfExists('mock_exam_subscription_plans');
    }
};
