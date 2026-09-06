<?php

use App\Enums\PurchaseType;
use App\Models\Purchase;
use App\Models\User;

test('purchase model can be created with factory and enum cast', function () {
    $purchase = Purchase::factory()->create([
        'type' => PurchaseType::Modal,
        'year' => 2026,
        'description' => 'Pengadaan Switch Core Kanwil 2026',
    ]);

    expect($purchase->type)->toBe(PurchaseType::Modal)
        ->and($purchase->type->code())->toBe('53')
        ->and($purchase->year)->toBe(2026)
        ->and($purchase->description)->toBe('Pengadaan Switch Core Kanwil 2026');

    $this->assertDatabaseHas('purchases', [
        'id' => $purchase->id,
        'type' => 'modal',
        'year' => 2026,
        'description' => 'Pengadaan Switch Core Kanwil 2026',
    ]);
});

test('purchase type helper returns correct mak code', function () {
    expect(PurchaseType::Modal->code())->toBe('53')
        ->and(PurchaseType::Pemeliharaan->code())->toBe('52');
});

test('unauthenticated users cannot access purchases endpoints', function () {
    $this->getJson('/api/purchases')->assertUnauthorized();
    $this->postJson('/api/purchases', [])->assertUnauthorized();
    $this->getJson('/api/purchases/1')->assertUnauthorized();
    $this->putJson('/api/purchases/1', [])->assertUnauthorized();
    $this->deleteJson('/api/purchases/1')->assertUnauthorized();
});

test('authenticated user can list purchases', function () {
    $user = User::factory()->create();
    Purchase::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/purchases');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
        ])
        ->assertJsonCount(3, 'data');
});

test('authenticated user can create a purchase', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/purchases', [
        'type' => 'modal',
        'year' => 2026,
        'description' => 'Pengadaan Switch Core Kanwil',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'message' => 'Purchase created successfully',
            'data' => [
                'type' => 'modal',
                'year' => 2026,
                'description' => 'Pengadaan Switch Core Kanwil',
            ],
        ]);

    $this->assertDatabaseHas('purchases', [
        'type' => 'modal',
        'year' => 2026,
        'description' => 'Pengadaan Switch Core Kanwil',
    ]);
});

test('create purchase validates required and enum fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/purchases', [
        'type' => 'invalid-type',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'success',
            'status',
            'message',
            'data' => ['type', 'year'],
        ]);
});

test('authenticated user can view a purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->create([
        'type' => PurchaseType::Pemeliharaan,
        'year' => 2025,
        'description' => 'Maintenance Firewall',
    ]);

    $response = $this->actingAs($user)->getJson("/api/purchases/{$purchase->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'data' => [
                'id' => $purchase->id,
                'type' => 'pemeliharaan',
                'year' => 2025,
                'description' => 'Maintenance Firewall',
            ],
        ]);
});

test('authenticated user can update a purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->create([
        'type' => PurchaseType::Modal,
        'year' => 2025,
        'description' => 'Old Description',
    ]);

    $response = $this->actingAs($user)->putJson("/api/purchases/{$purchase->id}", [
        'type' => 'pemeliharaan',
        'description' => 'Updated Description',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Purchase updated successfully',
            'data' => [
                'id' => $purchase->id,
                'type' => 'pemeliharaan',
                'description' => 'Updated Description',
            ],
        ]);

    $this->assertDatabaseHas('purchases', [
        'id' => $purchase->id,
        'type' => 'pemeliharaan',
        'description' => 'Updated Description',
    ]);
});

test('authenticated user can delete a purchase', function () {
    $user = User::factory()->create();
    $purchase = Purchase::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/purchases/{$purchase->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Purchase deleted successfully',
        ]);

    $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
});
