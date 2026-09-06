<?php

namespace Database\Factories;

use App\Enums\AssetCategory;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(AssetCategory::cases()),
            'name' => fake()->randomElement(['Switch Cisco Catalyst 2960', 'Router MikroTik CCR1009', 'Firewall FortiGate 100F', 'Lisensi Antivirus Endpoint', 'Lisensi Windows Server']),
            'number' => strtoupper(fake()->bothify('AST-####-????')),
            'unit_price' => fake()->numberBetween(1_000_000, 50_000_000),
            'end_date' => fake()->dateTimeBetween('+1 year', '+3 years')->format('Y-m-d'),
            'notes' => fake()->sentence(),
        ];
    }
}
