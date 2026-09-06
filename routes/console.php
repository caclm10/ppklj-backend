<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('make:user {username?} {name?} {--password=}', function () {
    $username = $this->argument('username') ?? $this->ask('Username');
    $name = $this->argument('name') ?? $this->ask('Full Name');
    $password = $this->option('password') ?? $this->secret('Password');

    if (User::where('username', $username)->exists()) {
        $this->error("Username '{$username}' already exists.");

        return 1;
    }

    $user = User::create([
        'username' => $username,
        'name' => $name,
        'password' => $password,
    ]);

    $this->info("User '{$user->username}' created successfully.");
})->purpose('Create a new user');
