<?php

namespace Database\Factories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Feature>
 */
class FeatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['PoE+', 'VLAN', 'L3 Routing', 'SFP+ 10G', 'SNMP', 'BGP', 'OSPF', 'QoS', 'VPN Server', 'Layer 2']),
        ];
    }
}
