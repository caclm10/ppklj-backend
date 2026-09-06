<?php

use App\Enums\AssetCategory;
use App\Enums\NetworkAssetStatus;
use App\Models\Asset;
use App\Models\Feature;
use App\Models\NetworkAsset;
use App\Models\Office;
use App\Models\User;

test('unauthenticated users cannot access features or network-assets', function () {
    $this->getJson('/api/features')->assertUnauthorized();
    $this->getJson('/api/network-assets')->assertUnauthorized();
});

test('authenticated user can list and create features', function () {
    $user = User::factory()->create();
    Feature::factory()->create(['name' => 'PoE+']);

    $listResponse = $this->actingAs($user)->getJson('/api/features');
    $listResponse->assertOk()->assertJsonCount(1, 'data');

    $createResponse = $this->actingAs($user)->postJson('/api/features', [
        'name' => 'VLAN',
    ]);
    $createResponse->assertCreated()->assertJson(['data' => ['name' => 'VLAN']]);

    $this->assertDatabaseHas('features', ['name' => 'VLAN']);
});

test('authenticated user can create network asset with status, office, and auto-create features', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['category' => AssetCategory::Jaringan]);
    $office = Office::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/network-assets', [
        'asset_id' => $asset->id,
        'office_id' => $office->id,
        'status' => 'aktif',
        'brand' => 'Cisco',
        'model' => 'WS-C2960X-48FPS-L',
        'type' => 'Switch Access',
        'ip' => '192.168.1.10',
        'hostname' => 'SW-ACC-01',
        'features' => ['PoE+', 'VLAN', 'L3 Routing'],
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'data' => [
                'asset_id' => $asset->id,
                'office_id' => $office->id,
                'status' => 'aktif',
                'brand' => 'Cisco',
                'model' => 'WS-C2960X-48FPS-L',
                'type' => 'Switch Access',
                'ip' => '192.168.1.10',
                'hostname' => 'SW-ACC-01',
            ],
        ]);

    $this->assertDatabaseHas('features', ['name' => 'PoE+']);
    $this->assertDatabaseHas('features', ['name' => 'VLAN']);
    $this->assertDatabaseHas('features', ['name' => 'L3 Routing']);

    $networkAsset = NetworkAsset::where('asset_id', $asset->id)->first();
    expect($networkAsset->features)->toHaveCount(3)
        ->and($networkAsset->status)->toBe(NetworkAssetStatus::Aktif)
        ->and($networkAsset->office->id)->toBe($office->id);
});

test('authenticated user can filter network assets by status and office', function () {
    $user = User::factory()->create();
    $office1 = Office::factory()->create();
    $office2 = Office::factory()->create();

    NetworkAsset::factory()->create([
        'office_id' => $office1->id,
        'status' => NetworkAssetStatus::Aktif,
    ]);
    NetworkAsset::factory()->create([
        'office_id' => $office2->id,
        'status' => NetworkAssetStatus::BelumDipasang,
    ]);

    $filteredStatus = $this->actingAs($user)->getJson('/api/network-assets?status=aktif');
    $filteredStatus->assertOk()->assertJsonCount(1, 'data');

    $filteredOffice = $this->actingAs($user)->getJson("/api/network-assets?office_id={$office1->id}");
    $filteredOffice->assertOk()->assertJsonCount(1, 'data');
});

test('authenticated user can update network asset status and office', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['category' => AssetCategory::Jaringan]);
    $office = Office::factory()->create();
    $networkAsset = NetworkAsset::factory()->create([
        'asset_id' => $asset->id,
        'status' => NetworkAssetStatus::BelumDipasang,
        'brand' => 'Cisco',
    ]);

    $response = $this->actingAs($user)->putJson("/api/network-assets/{$networkAsset->id}", [
        'office_id' => $office->id,
        'status' => 'aktif',
        'brand' => 'MikroTik',
        'features' => ['BGP', 'OSPF'],
    ]);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'office_id' => $office->id,
                'status' => 'aktif',
                'brand' => 'MikroTik',
            ],
        ]);

    expect($networkAsset->fresh()->features)->toHaveCount(2)
        ->and($networkAsset->fresh()->status)->toBe(NetworkAssetStatus::Aktif);
});

test('authenticated user can delete network asset', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['category' => AssetCategory::Jaringan]);
    $networkAsset = NetworkAsset::factory()->create(['asset_id' => $asset->id]);

    $response = $this->actingAs($user)->deleteJson("/api/network-assets/{$networkAsset->id}");
    $response->assertOk();

    $this->assertDatabaseMissing('network_assets', ['id' => $networkAsset->id]);
});
