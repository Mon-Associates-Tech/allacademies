# Subscription Merge Implementation

## Overview
This implementation handles the scenario where users purchase new subscriptions while having existing active subscriptions, merging overlapping cycles and preserving non-overlapping ones.

## Key Features

### 1. **Cycle Merging**
When a user purchases a new subscription that overlaps with existing cycles:
- **Overlapping months**: Tokens are combined (old + new)
- **Non-overlapping months**: Original cycles continue unchanged
- **Model priority**: Most recent purchase's model is used during overlap

### 2. **Example Scenario**
**Initial State:**
- User has Basic (1000 tokens/month) for 5 months (Jan-Jun)

**New Purchase:**
- User buys Premium (2000 tokens/month) for 3 months (Jan-Mar)

**Result:**
- **Jan**: 3000 tokens (1000 Basic + 2000 Premium), Premium model
- **Feb**: 3000 tokens (1000 Basic + 2000 Premium), Premium model
- **Mar**: 3000 tokens (1000 Basic + 2000 Premium), Premium model
- **Apr**: 1000 tokens (Basic only), Basic model
- **May**: 1000 tokens (Basic only), Basic model
- **Jun**: 1000 tokens (Basic only), Basic model

## Database Changes

### New Fields in `subscription_cycles` table:
```php
'merged_with_group_id' => string|null  // Links to the other subscription group
'is_merged' => boolean                  // Indicates if cycle is merged
```

## Core Logic

### TokenSubscriptionService::createSubscriptionCycles()
```php
// For each month in new purchase:
1. Check if existing cycle overlaps with this month
2. If overlap exists:
   - Merge: combine tokens, use new tier's model
   - Mark as merged with both group IDs
3. If no overlap:
   - Create new cycle normally
4. Activate only the first cycle (current month)
```

### TokenSubscriptionService::mergeCycles()
```php
// Merging logic:
- Combined tokens = old_base + new_base + old_topup
- Combined price = old_monthly_price + new_monthly_price
- Tier = new tier (most recent purchase)
- Preserve old topup tokens
```

## User Experience

### Single Combined View
Users see one unified subscription with:
- Current month active
- Future months pending
- Varying token allocations per month
- Automatic model switching based on active tier

### Topup Tokens
- Topup tokens from old subscription are preserved
- They carry over through merged cycles
- Base allocations reset each cycle as normal

## Payment Flow

1. User selects tier and duration
2. System creates/merges cycles
3. Payment is processed
4. First cycle activates immediately
5. Future cycles remain pending

## Migration Required

Run this migration to add new fields:
```bash
php artisan migrate
```

## Testing Scenarios

### Test Case 1: Same Duration
- Old: Basic 3 months
- New: Premium 3 months
- Result: 3 merged cycles

### Test Case 2: New Shorter
- Old: Basic 5 months
- New: Premium 3 months
- Result: 3 merged + 2 original Basic

### Test Case 3: New Longer
- Old: Basic 2 months
- New: Premium 5 months
- Result: 2 merged + 3 new Premium

### Test Case 4: No Overlap
- Old: Basic (expired)
- New: Premium 3 months
- Result: 3 new Premium cycles

## Important Notes

1. **Only current month is active** - Future cycles are pending
2. **Topup tokens are preserved** during merges
3. **Pricing is cumulative** - reflects total cost up to that cycle
4. **Model priority** - Most recent purchase determines the model
5. **User sees unified view** - Not two separate subscriptions

## Files Modified

1. `database/migrations/2024_01_15_000001_add_merged_fields_to_subscription_cycles.php`
2. `app/Models/Chat/SubscriptionCycle.php`
3. `app/Services/SubscriptionCycleService.php` (main merging logic)
4. `app/Http/Controllers/TokenSubscriptionController.php`

## Next Steps

1. Run migration: `php artisan migrate`
2. Test with different tier combinations
3. Verify token deduction works correctly across merged cycles
4. Update UI to show merged subscription details clearly
