<?php

use App\Enums\AssetCategory;
use App\Enums\PurchaseType;
use App\Models\Asset;
use App\Models\AssetPurchase;
use App\Models\Purchase;
use App\Models\User;

test('asset model can be created with factory and enum cast', function () {
    $asset = Asset::factory()->create([
        'category' => AssetCategory::Jaringan,
        'name' => 'Switch Catalyst 2960',
        'number' => 'SW-2960-01',
        'unit_price' => 15000000,
        'end_date' => '2028-12-31',
        'notes' => 'Switch Core Lantai 2',
    ]);

    expect($asset->category)->toBe(AssetCategory::Jaringan)
        ->and($asset->name)->toBe('Switch Catalyst 2960')
        ->and($asset->number)->toBe('SW-2960-01')
        ->and($asset->unit_price)->toBe(15000000)
        ->and($asset->end_date->format('Y-m-d'))->toBe('2028-12-31')
        ->and($asset->notes)->toBe('Switch Core Lantai 2');

    $this->assertDatabaseHas('assets', [
        'id' => $asset->id,
        'category' => 'jaringan',
        'name' => 'Switch Catalyst 2960',
        'number' => 'SW-2960-01',
        'unit_price' => 15000000,
    ]);
});

test('asset and asset purchase have many to many lifecycle history via pivot', function () {
    $purchaseModal = Purchase::factory()->create([
        'type' => PurchaseType::Modal,
        'year' => 2023,
    ]);

    $purchaseMaint = Purchase::factory()->create([
        'type' => PurchaseType::Pemeliharaan,
        'year' => 2024,
    ]);

    $assetPurchase1 = AssetPurchase::create([
        'purchase_id' => $purchaseModal->id,
        'name' => 'Pengadaan Aruba AP-635',
        'price' => 500000000,
        'quantity' => 100,
        'start_date' => '2023-01-01',
        'end_date' => '2026-01-01',
        'notes' => 'Belanja Modal 100 Unit AP',
    ]);

    $assetPurchase2 = AssetPurchase::create([
        'purchase_id' => $purchaseMaint->id,
        'name' => 'Perpanjangan Smartnet Aruba AP',
        'price' => 50000000,
        'quantity' => 100,
        'start_date' => '2024-01-01',
        'end_date' => '2027-01-01',
        'notes' => 'Belanja Pemeliharaan Garansi 100 Unit AP',
    ]);

    $asset1 = Asset::factory()->create([
        'name' => 'Aruba AP-635',
        'number' => 'AP-SN-001',
    ]);

    $asset2 = Asset::factory()->create([
        'name' => 'Aruba AP-635',
        'number' => 'AP-SN-002',
    ]);

    $assetPurchase1->assets()->attach([$asset1->id, $asset2->id]);
    $assetPurchase2->assets()->attach([$asset1->id, $asset2->id]);

    expect($assetPurchase1->assets)->toHaveCount(2)
        ->and($assetPurchase2->assets)->toHaveCount(2)
        ->and($asset1->fresh()->assetPurchases)->toHaveCount(2)
        ->and($asset2->fresh()->assetPurchases)->toHaveCount(2);

    $this->assertDatabaseHas('asset_asset_purchase', [
        'asset_purchase_id' => $assetPurchase1->id,
        'asset_id' => $asset1->id,
    ]);
    $this->assertDatabaseHas('asset_asset_purchase', [
        'asset_purchase_id' => $assetPurchase2->id,
        'asset_id' => $asset1->id,
    ]);
});

test('unauthenticated users cannot access assets or asset-purchases endpoints', function () {
    $this->getJson('/api/assets')->assertUnauthorized();
    $this->postJson('/api/assets', [])->assertUnauthorized();
    $this->getJson('/api/asset-purchases')->assertUnauthorized();
    $this->postJson('/api/asset-purchases', [])->assertUnauthorized();
});

test('authenticated user can list and filter assets', function () {
    $user = User::factory()->create();
    Asset::factory()->create(['category' => AssetCategory::Jaringan]);
    Asset::factory()->create(['category' => AssetCategory::License]);

    $response = $this->actingAs($user)->getJson('/api/assets');
    $response->assertOk()->assertJsonCount(2, 'data');

    $filteredResponse = $this->actingAs($user)->getJson('/api/assets?category=jaringan');
    $filteredResponse->assertOk()->assertJsonCount(1, 'data');
});

test('authenticated user can create an asset with optional asset_purchase_id', function () {
    $user = User::factory()->create();
    $assetPurchase = AssetPurchase::factory()->create([
        'end_date' => '2027-12-31',
    ]);

    $response = $this->actingAs($user)->postJson('/api/assets', [
        'asset_purchase_id' => $assetPurchase->id,
        'category' => 'jaringan',
        'name' => 'Router CCR2004',
        'number' => 'RTR-2004-01',
        'unit_price' => 8500000,
        'notes' => 'Router Utama',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'data' => [
                'category' => 'jaringan',
                'name' => 'Router CCR2004',
                'number' => 'RTR-2004-01',
                'unit_price' => 8500000,
                'end_date' => '2027-12-31',
            ],
        ]);

    $this->assertDatabaseHas('asset_asset_purchase', [
        'asset_purchase_id' => $assetPurchase->id,
        'asset_id' => $response->json('data.id'),
    ]);
});

test('authenticated user can view, update, and delete an asset', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['name' => 'Old Asset']);

    $showResponse = $this->actingAs($user)->getJson("/api/assets/{$asset->id}");
    $showResponse->assertOk()->assertJson(['data' => ['id' => $asset->id]]);

    $updateResponse = $this->actingAs($user)->putJson("/api/assets/{$asset->id}", [
        'name' => 'Updated Asset Name',
    ]);
    $updateResponse->assertOk()->assertJson(['data' => ['name' => 'Updated Asset Name']]);

    $deleteResponse = $this->actingAs($user)->deleteJson("/api/assets/{$asset->id}");
    $deleteResponse->assertOk();

    $this->assertDatabaseMissing('assets', ['id' => $asset->id]);
});

test('authenticated user can record, update, and delete an asset purchase item with multiple assets', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create();
    $asset2 = Asset::factory()->create();
    $purchaseModal = Purchase::factory()->create(['type' => PurchaseType::Modal]);
    $purchaseMaint = Purchase::factory()->create(['type' => PurchaseType::Pemeliharaan]);

    // 1. Initial purchase (Belanja Modal)
    $storeResponse = $this->actingAs($user)->postJson('/api/asset-purchases', [
        'purchase_id' => $purchaseModal->id,
        'name' => 'Pengadaan Aruba AP-635 100 Unit',
        'price' => 500000000,
        'quantity' => 100,
        'start_date' => '2026-01-01',
        'end_date' => '2027-01-01',
        'notes' => 'Pengadaan Aset Q1',
        'asset_ids' => [$asset1->id, $asset2->id],
    ]);

    $storeResponse->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'data' => [
                'purchase_id' => $purchaseModal->id,
                'name' => 'Pengadaan Aruba AP-635 100 Unit',
                'price' => 500000000,
                'quantity' => 100,
            ],
        ]);

    expect($asset1->fresh()->end_date->format('Y-m-d'))->toBe('2027-01-01')
        ->and($asset2->fresh()->end_date->format('Y-m-d'))->toBe('2027-01-01')
        ->and($asset1->fresh()->assetPurchases)->toHaveCount(1);

    $assetPurchase1Id = $storeResponse->json('data.id');

    // 2. Second purchase (Belanja Pemeliharaan) - does NOT overwrite first purchase!
    $maintResponse = $this->actingAs($user)->postJson('/api/asset-purchases', [
        'purchase_id' => $purchaseMaint->id,
        'name' => 'Perpanjangan Smartnet Aruba AP',
        'price' => 60000000,
        'quantity' => 100,
        'start_date' => '2027-01-01',
        'end_date' => '2028-01-01',
        'notes' => 'Perpanjangan Garansi',
        'asset_ids' => [$asset1->id, $asset2->id],
    ]);

    $maintResponse->assertCreated();
    expect($asset1->fresh()->assetPurchases)->toHaveCount(2)
        ->and($asset1->fresh()->end_date->format('Y-m-d'))->toBe('2028-01-01');

    $assetPurchase2Id = $maintResponse->json('data.id');

    // 3. Filter asset purchases by asset_id (returns both lifecycle records)
    $assetFilterResponse = $this->actingAs($user)->getJson("/api/asset-purchases?asset_id={$asset1->id}");
    $assetFilterResponse->assertOk()->assertJsonCount(2, 'data');

    // 4. Update asset purchase
    $updateResponse = $this->actingAs($user)->putJson("/api/asset-purchases/{$assetPurchase2Id}", [
        'end_date' => '2029-01-01',
        'notes' => 'Revisi Garansi 2029',
    ]);
    $updateResponse->assertOk();
    expect($asset1->fresh()->end_date->format('Y-m-d'))->toBe('2029-01-01');

    // 5. Delete asset purchase
    $deleteResponse = $this->actingAs($user)->deleteJson("/api/asset-purchases/{$assetPurchase2Id}");
    $deleteResponse->assertOk();

    $this->assertDatabaseMissing('asset_purchases', ['id' => $assetPurchase2Id]);
    $this->assertDatabaseMissing('asset_asset_purchase', ['asset_purchase_id' => $assetPurchase2Id]);

    expect($asset1->fresh())->not->toBeNull()
        ->and($asset1->fresh()->assetPurchases)->toHaveCount(1);
});

test('license maintenance quantity cannot exceed its source package quantity', function () {
    $user = User::factory()->create();
    $sourcePurchase = Purchase::factory()->create(['type' => PurchaseType::Modal]);
    $maintenancePurchase = Purchase::factory()->create(['type' => PurchaseType::Pemeliharaan]);
    $sourcePackage = AssetPurchase::factory()->create([
        'purchase_id' => $sourcePurchase->id,
        'quantity' => 120,
    ]);
    $license = Asset::factory()->create(['category' => AssetCategory::License]);
    $sourcePackage->assets()->attach($license);

    $response = $this->actingAs($user)->postJson('/api/asset-purchases', [
        'purchase_id' => $maintenancePurchase->id,
        'name' => 'Perpanjangan Lisensi PostgreSQL',
        'quantity' => 121,
        'asset_ids' => [$license->id],
    ]);

    $response->assertUnprocessable()
        ->assertJsonPath('data.quantity.0', 'Quantity lisensi tidak boleh melebihi quantity paket pengadaan asal.');

    $validResponse = $this->actingAs($user)->postJson('/api/asset-purchases', [
        'purchase_id' => $maintenancePurchase->id,
        'name' => 'Perpanjangan Sebagian Lisensi PostgreSQL',
        'quantity' => 60,
        'asset_ids' => [$license->id],
    ]);

    $validResponse->assertCreated()
        ->assertJsonPath('data.quantity', 60);

    expect($sourcePackage->fresh()->quantity)->toBe(120);
});
