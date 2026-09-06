<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AssetPurchase;
use App\Models\Purchase;
use Illuminate\Database\Seeder;

class AssetPurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = Asset::all();
        $purchases = Purchase::all();

        if ($assets->isEmpty()) {
            $assets = Asset::factory()->count(5)->create();
        }

        if ($purchases->isEmpty()) {
            $purchases = Purchase::factory()->count(3)->create();
        }

        foreach ($assets as $asset) {
            AssetPurchase::factory()->create([
                'asset_id' => $asset->id,
                'purchase_id' => $purchases->random()->id,
            ]);
        }
    }
}
