<?php

namespace App\Models;

use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name'])]
class Feature extends Model
{
    /** @use HasFactory<FeatureFactory> */
    use HasFactory;

    /**
     * Get the network assets associated with this feature.
     */
    public function networkAssets(): BelongsToMany
    {
        return $this->belongsToMany(NetworkAsset::class, 'feature_network_asset')
            ->withTimestamps();
    }
}
