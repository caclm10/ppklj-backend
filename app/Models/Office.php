<?php

namespace App\Models;

use App\Enums\OfficeType;
use Database\Factories\OfficeFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['type', 'name'])]
class Office extends Model
{
    /** @use HasFactory<OfficeFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => OfficeType::class,
        ];
    }

    /**
     * Get the network devices installed in this office.
     */
    public function networkAssets(): HasMany
    {
        return $this->hasMany(NetworkAsset::class);
    }
}
