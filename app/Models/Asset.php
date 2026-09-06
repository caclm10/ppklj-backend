<?php

namespace App\Models;

use App\Enums\AssetCategory;
use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['category', 'name', 'number', 'unit_price', 'end_date', 'notes'])]
class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => AssetCategory::class,
            'unit_price' => 'integer',
            'end_date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the purchase records associated with this asset throughout its lifecycle.
     */
    public function assetPurchases(): BelongsToMany
    {
        return $this->belongsToMany(AssetPurchase::class, 'asset_asset_purchase')->withTimestamps();
    }

    /**
     * Get the network asset details if this is a network hardware asset.
     */
    public function networkAsset(): HasOne
    {
        return $this->hasOne(NetworkAsset::class);
    }

    /**
     * Get the RMA history tickets for this asset.
     */
    public function rmas(): HasMany
    {
        return $this->hasMany(AssetRma::class)->latest();
    }
}
