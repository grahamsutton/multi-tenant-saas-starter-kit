<?php

use App\Models\User;
use Stripe\Service\BillingPortal\SessionService;
use Stripe\StripeClient;

test('billing page redirects guests to login', function () {
    $this->get(route('billing.edit'))
        ->assertRedirect(route('login'));
});

test('billing page redirects to stripe billing portal', function () {
    $portalUrl = 'https://billing.stripe.com/p/session/test_123';

    $user = User::factory()->create();
    $user->currentOrganization->forceFill(['stripe_id' => 'cus_test_123'])->save();

    $sessionService = Mockery::mock(SessionService::class);
    $sessionService->shouldReceive('create')
        ->once()
        ->andReturn(new ArrayObject(['url' => $portalUrl]));

    $stripeClient = Mockery::mock(StripeClient::class)->makePartial();
    $stripeClient->billingPortal = new stdClass;
    $stripeClient->billingPortal->sessions = $sessionService;

    $this->app->bind(StripeClient::class, fn () => $stripeClient);

    $this->actingAs($user)
        ->get(route('billing.edit'))
        ->assertRedirect($portalUrl);
});
