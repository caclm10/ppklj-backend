<?php

use App\Models\User;

test('creates a user via artisan arguments', function () {
    $this->artisan('make:user', [
        'username' => 'lewin',
        'name' => 'Lewin Xander',
        '--password' => 'secret123',
    ])
        ->expectsOutput("User 'lewin' created successfully.")
        ->assertSuccessful();

    $this->assertDatabaseHas('users', [
        'username' => 'lewin',
        'name' => 'Lewin Xander',
    ]);
});

test('prevents creating duplicate username', function () {
    User::factory()->create(['username' => 'lewin']);

    $this->artisan('make:user', [
        'username' => 'lewin',
        'name' => 'Another Lewin',
        '--password' => 'secret123',
    ])
        ->expectsOutput("Username 'lewin' already exists.")
        ->assertFailed();
});
