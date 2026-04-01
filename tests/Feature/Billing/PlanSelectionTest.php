<?php

use App\Models\User;

test('guests cannot access the plans page', function () {
    $response = $this->get(route('billing.plans'));

    $response->assertRedirect(route('login'));
});

test('authenticated users can view the plans page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('billing.plans'));

    $response->assertOk();
});

test('subscribed users are redirected from plans page to dashboard', function () {
    $user = User::factory()->create();
    $organization = $user->currentOrganization;
    $organization->subscriptions()->create([
        'type' => 'default',
        'stripe_id' => 'sub_test_'.fake()->regexify('[A-Za-z0-9]{14}'),
        'stripe_status' => 'active',
        'stripe_price' => 'price_test',
    ]);

    $this->actingAs($user);

    $response = $this->get(route('billing.plans'));

    $response->assertRedirect(route('dashboard'));
});

test('checkout requires a valid plan and interval', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('checkout.store'), []);

    $response->assertSessionHasErrors(['plan', 'interval']);
});

test('checkout rejects an invalid plan slug', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('checkout.store'), [
        'plan' => 'nonexistent',
        'interval' => 'monthly',
    ]);

    $response->assertSessionHasErrors('plan');
});

test('checkout rejects an invalid interval', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('checkout.store'), [
        'plan' => 'starter',
        'interval' => 'weekly',
    ]);

    $response->assertSessionHasErrors('interval');
});
