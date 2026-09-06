<?php

use App\Enums\AssetCategory;
use App\Enums\NetworkAssetStatus;
use App\Enums\RmaResolution;
use App\Enums\RmaStatus;
use App\Models\Asset;
use App\Models\AssetRma;
use App\Models\NetworkAsset;
use App\Models\User;

test('unauthenticated users cannot access rma endpoints', function () {
    $this->getJson('/api/asset-rmas')->assertUnauthorized();
    $this->postJson('/api/asset-rmas', [])->assertUnauthorized();
    $this->getJson('/api/asset-rmas/1')->assertUnauthorized();
    $this->postJson('/api/asset-rmas/1/tracks', [])->assertUnauthorized();
    $this->deleteJson('/api/asset-rmas/1')->assertUnauthorized();
});

test('authenticated user can list rma tickets with filters', function () {
    $user = User::factory()->create();
    $asset1 = Asset::factory()->create();
    $asset2 = Asset::factory()->create();

    AssetRma::factory()->create([
        'asset_id' => $asset1->id,
        'current_status' => RmaStatus::RusakDiKantor,
    ]);
    AssetRma::factory()->create([
        'asset_id' => $asset2->id,
        'current_status' => RmaStatus::SelesaiDipasang,
        'resolution' => RmaResolution::Diperbaiki,
    ]);

    $response = $this->actingAs($user)->getJson('/api/asset-rmas');
    $response->assertOk()->assertJsonCount(2, 'data');

    $filteredStatus = $this->actingAs($user)->getJson('/api/asset-rmas?current_status=rusak_di_kantor');
    $filteredStatus->assertOk()->assertJsonCount(1, 'data');

    $filteredAsset = $this->actingAs($user)->getJson("/api/asset-rmas?asset_id={$asset1->id}");
    $filteredAsset->assertOk()->assertJsonCount(1, 'data');
});

test('authenticated user can create rma ticket which captures old serial and creates initial track', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create([
        'category' => AssetCategory::Jaringan,
        'number' => 'SN-CISCO-OLD-1234',
    ]);
    $networkAsset = NetworkAsset::factory()->create([
        'asset_id' => $asset->id,
        'status' => NetworkAssetStatus::Aktif,
    ]);

    $response = $this->actingAs($user)->postJson('/api/asset-rmas', [
        'asset_id' => $asset->id,
        'pic_name' => 'Budi Santoso',
        'pic_phone' => '08123456789',
        'rma_number' => 'RMA-TAC-998811',
        'vendor_name' => 'Cisco Systems Indonesia',
        'problem_description' => 'Port Switch 1-12 mati setelah petir.',
        'notes' => 'Perangkat dicopot dari rak dan disimpan di ruang IT.',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'data' => [
                'asset_id' => $asset->id,
                'user_id' => $user->id,
                'pic_name' => 'Budi Santoso',
                'pic_phone' => '08123456789',
                'old_serial_number' => 'SN-CISCO-OLD-1234',
                'current_status' => 'rusak_di_kantor',
            ],
        ]);

    $this->assertDatabaseHas('asset_rmas', [
        'asset_id' => $asset->id,
        'old_serial_number' => 'SN-CISCO-OLD-1234',
        'current_status' => 'rusak_di_kantor',
    ]);

    $this->assertDatabaseHas('asset_rma_tracks', [
        'status' => 'rusak_di_kantor',
        'notes' => 'Perangkat dicopot dari rak dan disimpan di ruang IT.',
    ]);

    expect($networkAsset->fresh()->status)->toBe(NetworkAssetStatus::TidakAktif);
});

test('authenticated user can view rma ticket and its tracks', function () {
    $user = User::factory()->create();
    $rma = AssetRma::factory()->create();
    $rma->tracks()->create([
        'status' => RmaStatus::RusakDiKantor,
        'notes' => 'Track 1',
    ]);

    $response = $this->actingAs($user)->getJson("/api/asset-rmas/{$rma->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'data' => [
                'id' => $rma->id,
                'tracks' => [
                    ['status' => 'rusak_di_kantor', 'notes' => 'Track 1'],
                ],
            ],
        ]);
});

test('authenticated user can update rma metadata', function () {
    $user = User::factory()->create();
    $rma = AssetRma::factory()->create(['pic_name' => 'Old PIC']);

    $response = $this->actingAs($user)->putJson("/api/asset-rmas/{$rma->id}", [
        'pic_name' => 'New PIC Updated',
        'vendor_name' => 'Fortinet TAC',
    ]);

    $response->assertOk()
        ->assertJson([
            'data' => [
                'pic_name' => 'New PIC Updated',
                'vendor_name' => 'Fortinet TAC',
            ],
        ]);
});

test('authenticated user can add progress tracking milestone to rma', function () {
    $user = User::factory()->create();
    $rma = AssetRma::factory()->create(['current_status' => RmaStatus::RusakDiKantor]);

    $response = $this->actingAs($user)->postJson("/api/asset-rmas/{$rma->id}/tracks", [
        'status' => 'pengiriman_ke_pusat',
        'notes' => 'Dikirim via JNE Cargo Resi: JNE123456789',
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'current_status' => 'pengiriman_ke_pusat',
            ],
        ]);

    expect($rma->fresh()->current_status)->toBe(RmaStatus::PengirimanKePusat);
    $this->assertDatabaseHas('asset_rma_tracks', [
        'asset_rma_id' => $rma->id,
        'status' => 'pengiriman_ke_pusat',
        'notes' => 'Dikirim via JNE Cargo Resi: JNE123456789',
    ]);
});

test('completing rma with diperbaiki resolution preserves serial number and reactivates network asset', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['number' => 'SN-ORIGINAL-999']);
    $networkAsset = NetworkAsset::factory()->create([
        'asset_id' => $asset->id,
        'status' => NetworkAssetStatus::TidakAktif,
    ]);
    $rma = AssetRma::factory()->create([
        'asset_id' => $asset->id,
        'old_serial_number' => 'SN-ORIGINAL-999',
        'current_status' => RmaStatus::PengirimanKeKantor,
    ]);

    $response = $this->actingAs($user)->postJson("/api/asset-rmas/{$rma->id}/tracks", [
        'status' => 'selesai_dipasang',
        'resolution' => 'diperbaiki',
        'notes' => 'Unit selesai diperbaiki dan dipasang kembali ke rak server.',
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'current_status' => 'selesai_dipasang',
                'resolution' => 'diperbaiki',
            ],
        ]);

    expect($asset->fresh()->number)->toBe('SN-ORIGINAL-999')
        ->and($networkAsset->fresh()->status)->toBe(NetworkAssetStatus::Aktif)
        ->and($rma->fresh()->completed_at)->not->toBeNull();
});

test('completing rma with diganti_unit resolution updates asset serial number to new serial', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->create(['number' => 'SN-BROKEN-OLD']);
    $networkAsset = NetworkAsset::factory()->create([
        'asset_id' => $asset->id,
        'status' => NetworkAssetStatus::TidakAktif,
    ]);
    $rma = AssetRma::factory()->create([
        'asset_id' => $asset->id,
        'old_serial_number' => 'SN-BROKEN-OLD',
        'current_status' => RmaStatus::PengirimanKeKantor,
    ]);

    $response = $this->actingAs($user)->postJson("/api/asset-rmas/{$rma->id}/tracks", [
        'status' => 'selesai_dipasang',
        'resolution' => 'diganti_unit',
        'new_serial_number' => 'SN-REPLACEMENT-NEW-2026',
        'notes' => 'Unit baru dari Cisco sudah diterima dan dipasang.',
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'current_status' => 'selesai_dipasang',
                'resolution' => 'diganti_unit',
                'old_serial_number' => 'SN-BROKEN-OLD',
                'new_serial_number' => 'SN-REPLACEMENT-NEW-2026',
            ],
        ]);

    expect($asset->fresh()->number)->toBe('SN-REPLACEMENT-NEW-2026')
        ->and($networkAsset->fresh()->status)->toBe(NetworkAssetStatus::Aktif)
        ->and($rma->fresh()->completed_at)->not->toBeNull();
});

test('authenticated user can delete rma ticket', function () {
    $user = User::factory()->create();
    $rma = AssetRma::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/asset-rmas/{$rma->id}");
    $response->assertOk();

    $this->assertDatabaseMissing('asset_rmas', ['id' => $rma->id]);
});
