<?php

namespace Database\Factories;

use App\Enums\RmaStatus;
use App\Models\AssetRma;
use App\Models\AssetRmaTrack;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetRmaTrack>
 */
class AssetRmaTrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'asset_rma_id' => AssetRma::factory(),
            'status' => fake()->randomElement(RmaStatus::cases()),
            'notes' => fake()->sentence(),
            'tracked_at' => now(),
        ];
    }
}
