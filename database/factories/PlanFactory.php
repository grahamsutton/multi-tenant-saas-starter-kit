<?php

namespace Database\Factories;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'stripe_monthly_price_id' => 'price_'.fake()->regexify('[A-Za-z0-9]{24}'),
            'stripe_annual_price_id' => 'price_'.fake()->regexify('[A-Za-z0-9]{24}'),
            'monthly_price' => fake()->randomElement([2900, 4900, 9900, 19900]),
            'annual_price' => fake()->randomElement([29000, 49000, 99000, 199000]),
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
            'trial_days' => 0,
        ];
    }

    /**
     * Indicate that the plan is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
