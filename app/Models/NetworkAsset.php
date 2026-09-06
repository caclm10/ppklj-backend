<?php

namespace App\Models;

use App\Enums\NetworkAssetStatus;
use Database\Factories\NetworkAssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['asset_id', 'office_id', 'status', 'brand', 'model', 'type', 'ip', 'hostname'])]
class NetworkAsset extends Model
{
    /** @use HasFactory<NetworkAssetFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => NetworkAssetStatus::class,
        ];
    }

    /**
     * Get the parent asset record.
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    /**
     * Get the office where this network device is located.
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    /**
     * Get the features/capabilities associated with this network asset.
     */
    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class, 'feature_network_asset')
            ->withTimestamps();
    }
}
