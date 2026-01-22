<?php

namespace Tests\Feature;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\Chat\UserTokenSubscription;
use App\Models\Payment;
use App\Models\User;
use App\Support\TokenSubscriptionStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenSubscriptionPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected PricingTier $pricingTier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
            'password' => 'password',
            'school_id' => 1,
        ]);

        $this->pricingTier = PricingTier::create([
            'name' => 'Test Tier',
            'model' => 'gpt-4',
            'base_price' => 10.00,
            'initial_price' => 15.00,
            'subsequent_price' => 12.00,
            'monthly_token_limit' => 10000,
            'initial_period_months' => 6,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function cycles_are_created_as_pending_before_payment()
    {
        $this->actingAs($this->user);

        $response = $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 3,
        ]);

        // Check cycles were created
        $cycles = SubscriptionCycle::where('user_id', $this->user->id)->get();
        
        $this->assertGreaterThan(0, $cycles->count(), 'No cycles were created');

        // All cycles should be inactive with 0 tokens (pending payment)
        foreach ($cycles as $cycle) {
            $this->assertEquals('inactive', $cycle->status);
            $this->assertEquals(0, $cycle->tokens_allocated, 'Tokens should be 0 before payment');
            $this->assertNotNull($cycle->subscription_group_id);
        }
        
        // User should not have tokens before payment
        $this->assertFalse($this->user->hasOpenAiTokens(1));
    }

    /** @test */
    public function user_cannot_use_tokens_before_payment()
    {
        $this->actingAs($this->user);

        // Ensure user has no existing cycles
        SubscriptionCycle::where('user_id', $this->user->id)->delete();

        // Create pending cycles
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 1,
        ]);

        // User should not have tokens
        $this->assertFalse($this->user->hasOpenAiTokens(1));
    }

    /** @test */
    public function cycles_are_activated_after_successful_payment()
    {
        $this->actingAs($this->user);

        // Create pending subscription
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 2,
        ]);

        $firstCycle = SubscriptionCycle::where('user_id', $this->user->id)->first();
        $this->assertNotNull($firstCycle, 'No cycle was created');
        
        $groupId = $firstCycle->subscription_group_id;
        $this->assertNotNull($groupId, 'Subscription group ID is null');

        // Simulate successful payment
        Payment::create([
            'reference' => 'test-ref-' . time(),
            'amount' => 25.00,
            'currency' => 'GHS',
            'status' => \App\Enums\PaymentStatus::SUCCEEDED,
        ]);

        // Activate cycles
        app(\App\Services\SubscriptionCycleService::class)
            ->activatePendingCycles($groupId, $this->pricingTier);

        // Check first cycle is active
        $firstCycle->refresh();

        $this->assertEquals('active', $firstCycle->status);
        $this->assertEquals($this->pricingTier->monthly_token_limit, $firstCycle->tokens_allocated);

        // Second cycle should be inactive (future)
        $secondCycle = SubscriptionCycle::where('user_id', $this->user->id)
            ->where('cycle_number', 2)
            ->first();

        $this->assertEquals('inactive', $secondCycle->status);
        $this->assertEquals($this->pricingTier->monthly_token_limit, $secondCycle->tokens_allocated);
    }

    /** @test */
    public function user_can_use_tokens_after_payment()
    {
        $this->actingAs($this->user);

        // Create and activate subscription
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 1,
        ]);

        $firstCycle = SubscriptionCycle::where('user_id', $this->user->id)->first();
        $this->assertNotNull($firstCycle);
        
        $groupId = $firstCycle->subscription_group_id;
        $this->assertNotNull($groupId);

        Payment::create([
            'reference' => 'test-ref-' . time(),
            'amount' => 10.00,
            'currency' => 'GHS',
            'status' => \App\Enums\PaymentStatus::SUCCEEDED,
        ]);

        app(\App\Services\SubscriptionCycleService::class)
            ->activatePendingCycles($groupId, $this->pricingTier);

        // Refresh user
        $this->user->refresh();

        // User should now have tokens
        $this->assertTrue($this->user->hasOpenAiTokens(1));
    }

    /** @test */
    public function abandoned_payment_leaves_no_active_tokens()
    {
        $this->actingAs($this->user);

        // Create pending subscription
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 1,
        ]);

        // User abandons payment (no payment record created)
        // User should not have any active tokens
        $this->assertFalse($this->user->hasOpenAiTokens(1));

        $activeCycles = SubscriptionCycle::where('user_id', $this->user->id)
            ->where('status', 'active')
            ->count();

        $this->assertEquals(0, $activeCycles);
    }

    /** @test */
    public function revoke_command_identifies_unpaid_cycles()
    {
        // Create a cycle without payment
        $cycle = SubscriptionCycle::create([
            'user_id' => $this->user->id,
            'pricing_tier_id' => $this->pricingTier->id,
            'subscription_group_id' => \Illuminate\Support\Str::uuid()->toString(),
            'cycle_number' => 1,
            'cycle_start_date' => now(),
            'cycle_end_date' => now()->addDays(30),
            'tokens_allocated' => 10000,
            'tokens_used' => 0,
            'current_price' => 10.00,
            'status' => 'active',
            'is_trial' => false,
            'allocated_by_admin' => false,
        ]);

        $this->artisan('tokens:revoke-unpaid --dry-run')
            ->expectsOutput('Found 1 unpaid cycles:')
            ->assertExitCode(0);
    }

    /** @test */
    public function revoke_command_does_not_affect_paid_cycles()
    {
        $this->actingAs($this->user);

        // Create and pay for subscription
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 1,
        ]);

        $firstCycle = SubscriptionCycle::where('user_id', $this->user->id)->first();
        $this->assertNotNull($firstCycle);
        
        $groupId = $firstCycle->subscription_group_id;
        $this->assertNotNull($groupId);

        Payment::create([
            'reference' => 'test-ref-' . time(),
            'amount' => 10.00,
            'currency' => 'GHS',
            'status' => \App\Enums\PaymentStatus::SUCCEEDED,
        ]);

        app(\App\Services\SubscriptionCycleService::class)
            ->activatePendingCycles($groupId, $this->pricingTier);

        $this->artisan('tokens:revoke-unpaid --dry-run')
            ->expectsOutput('No unpaid cycles found. All cycles have corresponding payments.')
            ->assertExitCode(0);
    }

    /** @test */
    public function admin_can_deactivate_cycle()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'password' => 'password',
        ]);

        $cycle = SubscriptionCycle::create([
            'user_id' => $this->user->id,
            'pricing_tier_id' => $this->pricingTier->id,
            'cycle_number' => 1,
            'cycle_start_date' => now(),
            'cycle_end_date' => now()->addDays(30),
            'tokens_allocated' => 10000,
            'tokens_used' => 0,
            'current_price' => 10.00,
            'status' => 'active',
            'allocated_by_admin' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('token-allocations.deactivate-cycle', $cycle->id))
            ->assertRedirect();

        $cycle->refresh();
        $this->assertEquals('inactive', $cycle->status);
    }

    /** @test */
    public function admin_can_revoke_tokens()
    {
        $admin = User::factory()->create([
            'role' => 'superadmin',
            'password' => 'password',
        ]);

        $cycle = SubscriptionCycle::create([
            'user_id' => $this->user->id,
            'pricing_tier_id' => $this->pricingTier->id,
            'cycle_number' => 1,
            'cycle_start_date' => now(),
            'cycle_end_date' => now()->addDays(30),
            'tokens_allocated' => 10000,
            'tokens_used' => 500,
            'current_price' => 10.00,
            'status' => 'active',
            'allocated_by_admin' => true,
        ]);

        $this->actingAs($admin)
            ->delete(route('token-allocations.revoke-tokens', $cycle->id))
            ->assertRedirect();

        $cycle->refresh();
        $this->assertEquals('cancelled', $cycle->status);
        $this->assertEquals(0, $cycle->tokens_allocated);
        $this->assertEquals(0, $cycle->tokens_used);
    }

    /** @test */
    public function multiple_month_subscription_creates_correct_number_of_cycles()
    {
        $this->actingAs($this->user);

        // Ensure no existing cycles
        SubscriptionCycle::where('user_id', $this->user->id)->delete();

        $months = 6;
        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => $months,
        ]);

        $cycles = SubscriptionCycle::where('user_id', $this->user->id)->get();
        $this->assertCount($months, $cycles);

        // Verify cycle numbers
        for ($i = 1; $i <= $months; $i++) {
            $cycle = $cycles->firstWhere('cycle_number', $i);
            $this->assertNotNull($cycle);
            $this->assertEquals('inactive', $cycle->status);
        }
    }

    /** @test */
    public function pricing_is_calculated_correctly_for_multi_month()
    {
        $this->actingAs($this->user);

        $this->post(route('token-subscriptions.process-payment'), [
            'pricing_tier_id' => $this->pricingTier->id,
            'months' => 3,
        ]);

        $cycles = SubscriptionCycle::where('user_id', $this->user->id)
            ->orderBy('cycle_number')
            ->get();

        // Pricing should be calculated even for pending cycles
        // Month 1: base_price (10)
        $this->assertEquals(10.00, (float)$cycles[0]->current_price);

        // Month 2: base + initial (10 + 15 = 25)
        $this->assertEquals(25.00, (float)$cycles[1]->current_price);

        // Month 3: base + initial + initial (10 + 15 + 15 = 40)
        $this->assertEquals(40.00, (float)$cycles[2]->current_price);
    }
}
