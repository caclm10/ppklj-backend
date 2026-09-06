<?php

namespace Database\Factories;

use App\Enums\AssetCategory;
use App\Enums\NetworkAssetStatus;
use App\Models\Asset;
use App\Models\NetworkAsset;
use App\Models\Office;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NetworkAsset>
 */
class NetworkAssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory()->create(['category' => AssetCategory::Jaringan])->id,
            'office_id' => Office::factory(),
            'status' => fake()->randomElement(NetworkAssetStatus::cases()),
            'brand' => fake()->randomElement(['Cisco', 'MikroTik', 'Fortinet', 'Aruba', 'Ruijie']),
            'model' => fake()->randomElement(['WS-C2960X-48FPS-L', 'CCR2004-16G-2S+', 'FortiGate 100F', 'AP-505']),
            'type' => fake()->randomElement(['Switch', 'Router', 'Firewall', 'Access Point']),
            'ip' => fake()->ipv4(),
            'hostname' => strtoupper(fake()->bothify('DEV-##-??')),
        ];
    }
}
