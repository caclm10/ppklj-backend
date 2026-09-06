<?php

namespace App\Models;

use App\Enums\RmaStatus;
use Database\Factories\AssetRmaTrackFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'asset_rma_id',
    'status',
    'notes',
    'tracked_at',
])]
class AssetRmaTrack extends Model
{
    /** @use HasFactory<AssetRmaTrackFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => RmaStatus::class,
            'tracked_at' => 'datetime',
        ];
    }

    public function rma(): BelongsTo
    {
        return $this->belongsTo(AssetRma::class, 'asset_rma_id');
    }
}
