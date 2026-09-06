<?php

use App\Enums\OfficeType;
use App\Models\Office;
use App\Models\User;

test('unauthenticated users cannot access offices endpoints', function () {
    $this->getJson('/api/offices')->assertUnauthorized();
    $this->postJson('/api/offices', [])->assertUnauthorized();
    $this->getJson('/api/offices/1')->assertUnauthorized();
    $this->putJson('/api/offices/1', [])->assertUnauthorized();
    $this->deleteJson('/api/offices/1')->assertUnauthorized();
});

test('authenticated user can list offices', function () {
    $user = User::factory()->create();
    Office::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/offices');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
        ])
        ->assertJsonCount(3, 'data');
});

test('authenticated user can create an office', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/offices', [
        'type' => 'pusat',
        'name' => 'Kantor Pusat Gedung A',
    ]);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'status' => 201,
            'message' => 'Office created successfully',
            'data' => [
                'type' => 'pusat',
                'name' => 'Kantor Pusat Gedung A',
            ],
        ]);

    $this->assertDatabaseHas('offices', [
        'type' => 'pusat',
        'name' => 'Kantor Pusat Gedung A',
    ]);
});

test('create office validates required and enum fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/offices', [
        'type' => 'invalid-type',
    ]);

    $response->assertUnprocessable()
        ->assertJsonStructure([
            'success',
            'status',
            'message',
            'data' => ['type', 'name'],
        ]);
});

test('authenticated user can view an office', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create([
        'type' => OfficeType::Vertikal,
        'name' => 'Kantor Regional',
    ]);

    $response = $this->actingAs($user)->getJson("/api/offices/{$office->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'data' => [
                'id' => $office->id,
                'type' => 'vertikal',
                'name' => 'Kantor Regional',
            ],
        ]);
});

test('authenticated user can update an office', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create([
        'type' => OfficeType::Vertikal,
        'name' => 'Old Name',
    ]);

    $response = $this->actingAs($user)->putJson("/api/offices/{$office->id}", [
        'name' => 'New Name',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Office updated successfully',
            'data' => [
                'id' => $office->id,
                'name' => 'New Name',
            ],
        ]);

    $this->assertDatabaseHas('offices', [
        'id' => $office->id,
        'name' => 'New Name',
    ]);
});

test('authenticated user can delete an office', function () {
    $user = User::factory()->create();
    $office = Office::factory()->create();

    $response = $this->actingAs($user)->deleteJson("/api/offices/{$office->id}");

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Office deleted successfully',
        ]);

    $this->assertDatabaseMissing('offices', ['id' => $office->id]);
});
