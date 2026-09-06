<?php

namespace App\Models;

use App\Enums\RmaResolution;
use App\Enums\RmaStatus;
use Database\Factories\AssetRmaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'asset_id',
    'user_id',
    'pic_name',
    'pic_phone',
    'rma_number',
    'vendor_name',
    'current_status',
    'resolution',
    'old_serial_number',
    'new_serial_number',
    'problem_description',
    'completed_at',
])]
class AssetRma extends Model
{
    /** @use HasFactory<AssetRmaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'current_status' => RmaStatus::class,
            'resolution' => RmaResolution::class,
            'completed_at' => 'datetime',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(AssetRmaTrack::class)->latest('tracked_at');
    }
}
