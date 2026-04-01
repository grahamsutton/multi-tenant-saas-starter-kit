<?php

namespace App\Http\Controllers\Billing;

use App\Actions\Billing\SyncStripeSubscription;
use App\Enums\BillingInterval;
use App\Http\Controllers\Controller;
use App\Http\Requests\Billing\CheckoutRequest;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Cashier\Cashier;

class CheckoutController extends Controller
{
    /**
     * Create a Stripe Checkout session for the selected plan.
     */
    public function store(CheckoutRequest $request)
    {
        $plan = Plan::where('slug', $request->validated('plan'))->firstOrFail();
        $interval = BillingInterval::from($request->validated('interval'));
        $priceId = $plan->stripePriceId($interval);

        abort_unless($priceId, 422, 'This plan does not have a price configured for the selected interval.');

        $organization = $request->user()->currentOrganization;
        $builder = $organization->newSubscription('default', $priceId);

        if ($plan->trial_days > 0) {
            $builder->trialDays($plan->trial_days);
        }

        $checkout = $builder->checkout([
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('billing.plans'),
        ]);

        return Inertia::location($checkout->url);
    }

    /**
     * Handle the return from Stripe Checkout and sync the subscription locally.
     */
    public function success(Request $request, SyncStripeSubscription $sync): RedirectResponse
    {
        $sessionId = $request->query('session_id');

        if (! $sessionId) {
            return to_route('billing.plans');
        }

        $session = Cashier::stripe()->checkout->sessions->retrieve($sessionId, [
            'expand' => ['subscription.items.data.price'],
        ]);

        if (! $session->subscription) {
            return to_route('billing.plans');
        }

        $sync->execute($request->user()->currentOrganization, $session->subscription);

        return to_route('dashboard');
    }
}
