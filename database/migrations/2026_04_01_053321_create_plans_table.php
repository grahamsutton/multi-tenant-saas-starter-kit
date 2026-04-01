<?php

use App\Models\Plan;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('stripe_monthly_price_id')->nullable();
            $table->string('stripe_annual_price_id')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('trial_days')->default(0);
            $table->timestamps();
        });

        Plan::create([
            'name' => 'Starter',
            'slug' => 'starter',
            'stripe_monthly_price_id' => config('services.stripe.starter_monthly_price_id'),
            'stripe_annual_price_id' => config('services.stripe.starter_annual_price_id'),
            'sort_order' => 1,
            'trial_days' => 0,
        ]);

        Plan::create([
            'name' => 'Professional',
            'slug' => 'professional',
            'stripe_monthly_price_id' => config('services.stripe.professional_monthly_price_id'),
            'stripe_annual_price_id' => config('services.stripe.professional_annual_price_id'),
            'sort_order' => 2,
            'trial_days' => 14,
        ]);

        Plan::create([
            'name' => 'Enterprise',
            'slug' => 'enterprise',
            'stripe_monthly_price_id' => config('services.stripe.enterprise_monthly_price_id'),
            'stripe_annual_price_id' => config('services.stripe.enterprise_annual_price_id'),
            'sort_order' => 3,
            'trial_days' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
