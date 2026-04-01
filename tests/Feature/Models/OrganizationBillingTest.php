<?php

use App\Models\Organization;
use Illuminate\Support\Facades\Schema;
use Laravel\Cashier\Cashier;

test('organizations table has cashier billing columns', function () {
    expect(Schema::hasColumns('organizations', [
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
    ]))->toBeTrue();
});

test('cashier customer model is organization', function () {
    expect(Cashier::$customerModel)->toBe(Organization::class);
});
