<?php

use App\Models\User;

test('user can login with valid username and password', function () {
    $user = User::factory()->create([
        'username' => 'admin_it',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/login', [
        'username' => 'admin_it',
        'password' => 'secret123',
    ]);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => 'admin_it',
                ],
            ],
        ]);

    $this->assertAuthenticatedAs($user);
});

test('user cannot login with invalid password', function () {
    User::factory()->create([
        'username' => 'admin_it',
        'password' => 'secret123',
    ]);

    $response = $this->postJson('/api/login', [
        'username' => 'admin_it',
        'password' => 'wrongpassword',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'status' => 422,
            'data' => [
                'username' => [__('auth.failed')],
            ],
        ]);

    $this->assertGuest();
});

test('user cannot login with non-existent username', function () {
    $response = $this->postJson('/api/login', [
        'username' => 'unknown_user',
        'password' => 'secret123',
    ]);

    $response->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'status' => 422,
            'data' => [
                'username' => [__('auth.failed')],
            ],
        ]);

    $this->assertGuest();
});

test('login requires username and password', function () {
    $response = $this->postJson('/api/login', []);

    $response->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'status' => 422,
        ])
        ->assertJsonStructure([
            'success',
            'status',
            'message',
            'data' => ['username', 'password'],
        ]);
});

test('authenticated user can get their own user details', function () {
    $user = User::factory()->create(['username' => 'admin_it', 'name' => 'Admin IT']);

    $response = $this->actingAs($user)->getJson('/api/user');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'data' => [
                'id' => $user->id,
                'username' => 'admin_it',
                'name' => 'Admin IT',
            ],
        ]);
});

test('unauthenticated user cannot access /api/user', function () {
    $response = $this->getJson('/api/user');

    $response->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthenticated.',
        ]);
});

test('authenticated user can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->postJson('/api/logout');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'status' => 200,
            'message' => 'Logout successful',
        ]);

    $this->assertGuest();
});

test('unauthenticated user cannot logout', function () {
    $response = $this->postJson('/api/logout');

    $response->assertUnauthorized()
        ->assertJson([
            'success' => false,
            'status' => 401,
            'message' => 'Unauthenticated.',
        ]);
});
