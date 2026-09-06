<?php

namespace App\Models;

use App\Enums\PurchaseType;
use Database\Factories\PurchaseFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'year', 'description'])]
class Purchase extends Model
{
    /** @use HasFactory<PurchaseFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => PurchaseType::class,
            'year' => 'integer',
        ];
    }

    /**
     * Get the asset purchase records under this purchase budget.
     */
    public function assetPurchases(): HasMany
    {
        return $this->hasMany(AssetPurchase::class);
    }
}
