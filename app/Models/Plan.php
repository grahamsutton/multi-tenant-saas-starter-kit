<?php

namespace App\Models;

use App\Enums\BillingInterval;
use App\Models\Concerns\HasPrefixedUlid;
use Database\Factories\PlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'slug', 'stripe_monthly_price_id', 'stripe_annual_price_id', 'monthly_price', 'annual_price', 'sort_order', 'is_active', 'trial_days'])]
class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory, HasPrefixedUlid;

    protected static string $ulidPrefix = 'pln';

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'monthly_price' => 'integer',
            'annual_price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'trial_days' => 'integer',
        ];
    }

    /**
     * Get the Stripe price ID for a given billing interval.
     */
    public function stripePriceId(BillingInterval $interval): ?string
    {
        return match ($interval) {
            BillingInterval::Monthly => $this->stripe_monthly_price_id,
            BillingInterval::Annual => $this->stripe_annual_price_id,
        };
    }
}
