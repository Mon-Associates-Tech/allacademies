<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('openai_token_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model');
            $table->bigInteger('token_limit');
            $table->decimal('price', 10, 2);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_free')->default(false);
            $table->timestamps();
        });

        Schema::create('user_token_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('package_id')->constrained('openai_token_packages')->onDelete('cascade');
            $table->string('reference')->unique();
            $table->bigInteger('tokens_purchased');
            $table->bigInteger('tokens_used')->default(0);
            $table->bigInteger('tokens_remaining');
            $table->timestamp('purchased_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->enum('status', ['pending', 'active', 'expired', 'depleted', 'replaced'])->default('pending');
            $table->enum('action_type', ['trial', 'purchase', 'upgrade', 'downgrade'])->default('purchase');
            $table->foreignId('replaced_by_id')->nullable()->constrained('user_token_subscriptions')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'activated_at']);
        });

        Schema::create('openai_token_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subscription_id')->nullable()->constrained('user_token_subscriptions')->onDelete('set null');
            $table->string('model');
            $table->integer('prompt_tokens');
            $table->integer('completion_tokens');
            $table->integer('total_tokens');
            $table->text('request_type')->nullable();
            $table->text('context')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'created_at']);
            $table->index(['subscription_id', 'created_at']);
        });

        // Add token subscription relation to payments table
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'token_subscription_id')) {
                    $table->foreignId('token_subscription_id')->nullable()->constrained('user_token_subscriptions')->onDelete('SET NULL');
                }
            });
        }

        // Insert default packages including free trial
        DB::table('openai_token_packages')->insert([
            [
                'name' => 'Free Trial',
                'model' => 'gpt-4-nano',
                'token_limit' => 50000,
                'price' => 0.00,
                'description' => '7-day free trial with 5,000 tokens to get started',
                'is_active' => true,
                'is_free' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Basic',
                'model' => 'gpt-4-nano',
                'token_limit' => 500000,
                'price' => 20,
                'description' => 'Perfect for regular usage with 100K tokens',
                'is_active' => true,
                'is_free' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Premium',
                'model' => 'gpt-5',
                'token_limit' => 1000000,
                'price' => 40,
                'description' => 'Advanced usage with 100K tokens',
                'is_active' => true,
                'is_free' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (Schema::hasColumn('payments', 'token_subscription_id')) {
                    $table->dropForeign(['token_subscription_id']);
                    $table->dropColumn('token_subscription_id');
                }
            });
        }

        Schema::dropIfExists('openai_token_usage_logs');
        Schema::dropIfExists('user_token_subscriptions');
        Schema::dropIfExists('openai_token_packages');
    }
};
