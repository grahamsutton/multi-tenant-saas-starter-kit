<?php

use App\Enums\BillingInterval;
use App\Models\Plan;
use Illuminate\Database\QueryException;

test('plan id has pln_ prefix', function () {
    $plan = Plan::factory()->create();

    expect($plan->id)->toStartWith('pln_');
});

test('plan slug must be unique', function () {
    Plan::factory()->create(['slug' => 'test-plan']);

    expect(fn () => Plan::factory()->create(['slug' => 'test-plan']))
        ->toThrow(QueryException::class);
});

test('stripePriceId returns monthly price for monthly interval', function () {
    $plan = Plan::factory()->create([
        'stripe_monthly_price_id' => 'price_monthly_123',
        'stripe_annual_price_id' => 'price_annual_456',
    ]);

    expect($plan->stripePriceId(BillingInterval::Monthly))->toBe('price_monthly_123');
});

test('stripePriceId returns annual price for annual interval', function () {
    $plan = Plan::factory()->create([
        'stripe_monthly_price_id' => 'price_monthly_123',
        'stripe_annual_price_id' => 'price_annual_456',
    ]);

    expect($plan->stripePriceId(BillingInterval::Annual))->toBe('price_annual_456');
});

test('factory creates valid plan', function () {
    $plan = Plan::factory()->create();

    expect($plan)
        ->name->not->toBeEmpty()
        ->slug->not->toBeEmpty()
        ->is_active->toBeTrue()
        ->sort_order->toBeGreaterThan(0);
});

test('inactive state sets is_active to false', function () {
    $plan = Plan::factory()->inactive()->create();

    expect($plan->is_active)->toBeFalse();
});

test('professional plan has 14 day trial', function () {
    $professional = Plan::where('slug', 'professional')->first();

    if (! $professional) {
        $this->markTestSkipped('Professional plan not seeded');
    }

    expect($professional->trial_days)->toBe(14);
});
