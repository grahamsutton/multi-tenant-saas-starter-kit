<?php

namespace App\Actions\Billing;

use App\Models\Organization;
use Carbon\Carbon;
use Stripe\Subscription as StripeSubscription;

class SyncStripeSubscription
{
    /**
     * Sync a Stripe subscription to the local database if it doesn't already exist.
     *
     * This avoids the race condition where the user returns from Stripe Checkout
     * before the webhook creates the local subscription record.
     */
    public function execute(Organization $organization, StripeSubscription $stripeSubscription): void
    {
        if ($organization->subscriptions()->where('stripe_id', $stripeSubscription->id)->exists()) {
            return;
        }

        $subscription = $organization->subscriptions()->create([
            'type' => 'default',
            'stripe_id' => $stripeSubscription->id,
            'stripe_status' => $stripeSubscription->status,
            'stripe_price' => $stripeSubscription->items->data[0]->price->id ?? null,
            'quantity' => $stripeSubscription->items->data[0]->quantity ?? null,
            'trial_ends_at' => $stripeSubscription->trial_end
                ? Carbon::createFromTimestamp($stripeSubscription->trial_end)
                : null,
            'ends_at' => null,
        ]);

        foreach ($stripeSubscription->items->data as $item) {
            $subscription->items()->create([
                'stripe_id' => $item->id,
                'stripe_product' => $item->price->product,
                'stripe_price' => $item->price->id,
                'quantity' => $item->quantity,
            ]);
        }
    }
}
