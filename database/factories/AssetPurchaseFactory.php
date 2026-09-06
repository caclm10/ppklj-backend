<?php

namespace Database\Factories;

use App\Models\AssetPurchase;
use App\Models\Purchase;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetPurchase>
 */
class AssetPurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'purchase_id' => Purchase::factory(),
            'name' => fake()->randomElement(['Aruba AP-635', 'Cisco Catalyst 2960X', 'FortiGate 100F', 'MikroTik CCR2004']),
            'price' => fake()->numberBetween(1_000_000, 100_000_000),
            'quantity' => fake()->numberBetween(1, 100),
            'start_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('+1 year', '+3 years')->format('Y-m-d'),
            'notes' => fake()->sentence(),
        ];
    }
}
