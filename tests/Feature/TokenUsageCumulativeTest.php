<?php

namespace Tests\Feature;

use App\Models\Chat\OpenAiTokenUsageLog;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use App\Services\TokenUsageService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TokenUsageCumulativeTest extends TestCase
{
    protected TokenUsageService $tokenUsageService;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password')->default('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('subscription_cycles')) {
            Schema::create('subscription_cycles', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->integer('tokens_allocated');
                $table->integer('tokens_used')->default(0);
                $table->string('status')->default('active');
                $table->dateTime('cycle_start_date');
                $table->dateTime('cycle_end_date');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('open_ai_token_usage_logs')) {
            Schema::create('open_ai_token_usage_logs', function ($table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('subscription_cycle_id')->constrained('subscription_cycles')->cascadeOnDelete();
                $table->integer('prompt_tokens');
                $table->integer('completion_tokens');
                $table->string('request_type')->nullable();
                $table->string('model')->nullable();
                $table->timestamps();
            });
        }

        $this->tokenUsageService = app(TokenUsageService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('open_ai_token_usage_logs');
        Schema::dropIfExists('subscription_cycles');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }

    public function test_tokens_used_is_cumulative_across_requests(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 5000,
            'tokens_used' => 0,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 100,
            'output_tokens' => 50,
        ]);

        $cycle->refresh();
        $this->assertEquals(150, $cycle->tokens_used);
        $this->assertDatabaseCount(OpenAiTokenUsageLog::class, 1);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 150,
            'output_tokens' => 50,
        ]);

        $cycle->refresh();
        $this->assertEquals(350, $cycle->tokens_used);
        $this->assertDatabaseCount(OpenAiTokenUsageLog::class, 2);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 50,
            'output_tokens' => 25,
        ]);

        $cycle->refresh();
        $this->assertEquals(425, $cycle->tokens_used);
        $this->assertDatabaseCount(OpenAiTokenUsageLog::class, 3);
    }

    public function test_open_ai_token_usage_log_tracks_individual_requests(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 5000,
            'tokens_used' => 0,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 100,
            'output_tokens' => 50,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 150,
            'output_tokens' => 50,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 50,
            'output_tokens' => 25,
        ]);

        $logs = OpenAiTokenUsageLog::all();
        $this->assertEquals(3, $logs->count());

        $cycle->refresh();
        $logTotal = $logs->sum(function ($log) {
            return $log->prompt_tokens + $log->completion_tokens;
        });
        $this->assertEquals(425, $logTotal);
        $this->assertEquals($logTotal, $cycle->tokens_used);
    }

    public function test_subscription_cycle_tokens_used_matches_sum_of_logs(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 10000,
            'tokens_used' => 0,
        ]);

        $requests = [
            ['input_tokens' => 100, 'output_tokens' => 50],
            ['input_tokens' => 200, 'output_tokens' => 75],
            ['input_tokens' => 150, 'output_tokens' => 100],
            ['input_tokens' => 300, 'output_tokens' => 120],
        ];

        foreach ($requests as $usage) {
            $this->tokenUsageService->logUsage($user, $usage);
        }

        $cycle->refresh();

        $logTotal = OpenAiTokenUsageLog::where('subscription_cycle_id', $cycle->id)
            ->get()
            ->sum(function ($log) {
                return $log->prompt_tokens + $log->completion_tokens;
            });

        $expectedTotal = 150 + 275 + 250 + 420;

        $this->assertEquals($expectedTotal, $logTotal);
        $this->assertEquals($expectedTotal, $cycle->tokens_used);
    }

    public function test_insufficient_tokens_prevents_deduction_and_log_creation(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 100,
            'tokens_used' => 0,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 100,
            'output_tokens' => 50,
        ]);

        $cycle->refresh();

        $this->assertEquals(0, $cycle->tokens_used);
        $this->assertDatabaseCount(OpenAiTokenUsageLog::class, 0);
    }

    public function test_tokens_remaining_calculation(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 1000,
            'tokens_used' => 0,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 200,
            'output_tokens' => 100,
        ]);

        $cycle->refresh();
        $this->assertEquals(300, $cycle->tokens_used);
        $this->assertEquals(700, $cycle->getTokensRemainingAttribute());

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 150,
            'output_tokens' => 100,
        ]);

        $cycle->refresh();
        $this->assertEquals(550, $cycle->tokens_used);
        $this->assertEquals(450, $cycle->getTokensRemainingAttribute());
    }

    public function test_usage_percentage_calculation(): void
    {
        $user = $this->createUser();
        $cycle = SubscriptionCycle::create([
            'user_id' => $user->id,
            'status' => 'active',
            'cycle_start_date' => Carbon::now()->subDays(5),
            'cycle_end_date' => Carbon::now()->addDays(25),
            'tokens_allocated' => 1000,
            'tokens_used' => 0,
        ]);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 150,
            'output_tokens' => 100,
        ]);

        $cycle->refresh();
        $this->assertEquals(25.0, $cycle->usage_percentage);

        $this->tokenUsageService->logUsage($user, [
            'input_tokens' => 300,
            'output_tokens' => 200,
        ]);

        $cycle->refresh();
        $this->assertEquals(75.0, $cycle->usage_percentage);
    }
}
