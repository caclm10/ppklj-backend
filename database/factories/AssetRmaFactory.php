<?php

namespace Database\Factories;

use App\Enums\RmaStatus;
use App\Models\Asset;
use App\Models\AssetRma;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssetRma>
 */
class AssetRmaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $asset = Asset::factory()->create();

        return [
            'asset_id' => $asset->id,
            'user_id' => User::factory(),
            'pic_name' => fake()->name(),
            'pic_phone' => fake()->phoneNumber(),
            'rma_number' => 'RMA-'.fake()->numerify('####-????'),
            'vendor_name' => fake()->company(),
            'current_status' => RmaStatus::RusakDiKantor,
            'old_serial_number' => $asset->number,
            'problem_description' => fake()->sentence(),
        ];
    }
}
