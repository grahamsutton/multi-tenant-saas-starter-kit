<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users without a subscription are redirected to plans', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('billing.plans'));
});

test('subscribed users can visit the dashboard', function () {
    $user = User::factory()->create();
    $organization = $user->currentOrganization;
    $organization->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->regexify('[A-Za-z0-9]{14}'),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});
