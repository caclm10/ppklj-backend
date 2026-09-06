<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\AssetCategory $category
 * @property string $name
 * @property string $number
 * @property int $unit_price
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetPurchase> $assetPurchases
 * @property-read int|null $asset_purchases_count
 * @property-read \App\Models\NetworkAsset|null $networkAsset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetRma> $rmas
 * @property-read int|null $rmas_count
 * @method static \Database\Factories\AssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Asset whereUpdatedAt($value)
 */
	class Asset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $purchase_id
 * @property string $name
 * @property int $price
 * @property int $quantity
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Asset> $assets
 * @property-read int|null $assets_count
 * @property-read \App\Models\Purchase $purchase
 * @method static \Database\Factories\AssetPurchaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase wherePurchaseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetPurchase whereUpdatedAt($value)
 */
	class AssetPurchase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $user_id
 * @property string $pic_name
 * @property string|null $pic_phone
 * @property string|null $rma_number
 * @property string|null $vendor_name
 * @property \App\Enums\RmaStatus $current_status
 * @property \App\Enums\RmaResolution|null $resolution
 * @property string|null $old_serial_number
 * @property string|null $new_serial_number
 * @property string|null $problem_description
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetRmaTrack> $tracks
 * @property-read int|null $tracks_count
 * @property-read \App\Models\User|null $user
 * @method static \Database\Factories\AssetRmaFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereCurrentStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereNewSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereOldSerialNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma wherePicName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma wherePicPhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereProblemDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereResolution($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereRmaNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRma whereVendorName($value)
 */
	class AssetRma extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_rma_id
 * @property \App\Enums\RmaStatus $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon $tracked_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\AssetRma $rma
 * @method static \Database\Factories\AssetRmaTrackFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereAssetRmaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereTrackedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AssetRmaTrack whereUpdatedAt($value)
 */
	class AssetRmaTrack extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NetworkAsset> $networkAssets
 * @property-read int|null $network_assets_count
 * @method static \Database\Factories\FeatureFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feature whereUpdatedAt($value)
 */
	class Feature extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $asset_id
 * @property int|null $office_id
 * @property \App\Enums\NetworkAssetStatus $status
 * @property string $brand
 * @property string $model
 * @property string $type
 * @property string|null $ip
 * @property string|null $hostname
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Asset $asset
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feature> $features
 * @property-read int|null $features_count
 * @property-read \App\Models\Office|null $office
 * @method static \Database\Factories\NetworkAssetFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereAssetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereBrand($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereHostname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereModel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereOfficeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NetworkAsset whereUpdatedAt($value)
 */
	class NetworkAsset extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\OfficeType $type
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\NetworkAsset> $networkAssets
 * @property-read int|null $network_assets_count
 * @method static \Database\Factories\OfficeFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Office whereUpdatedAt($value)
 */
	class Office extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property \App\Enums\PurchaseType $type
 * @property int $year
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AssetPurchase> $assetPurchases
 * @property-read int|null $asset_purchases_count
 * @method static \Database\Factories\PurchaseFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Purchase whereYear($value)
 */
	class Purchase extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 */
	class User extends \Eloquent {}
}

