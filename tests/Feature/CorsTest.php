<?php

test('cors preflight request allows configured frontend origin with credentials', function () {
    $response = $this->withHeaders([
        'Origin' => 'http://localhost:3000',
        'Access-Control-Request-Method' => 'POST',
        'Access-Control-Request-Headers' => 'Content-Type, X-Requested-With, Accept',
    ])->options('/api/login');

    $response->assertStatus(204)
        ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->assertHeader('Access-Control-Allow-Credentials', 'true');
});

test('cors disallows unauthorized origin', function () {
    $response = $this->withHeaders([
        'Origin' => 'http://malicious-site.com',
        'Access-Control-Request-Method' => 'POST',
    ])->options('/api/login');

    $response->assertHeaderMissing('Access-Control-Allow-Origin');
});

test('sanctum csrf cookie endpoint is accessible for spa', function () {
    $response = $this->withHeaders([
        'Origin' => 'http://localhost:3000',
    ])->get('/sanctum/csrf-cookie');

    $response->assertNoContent()
        ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:3000')
        ->assertHeader('Access-Control-Allow-Credentials', 'true')
        ->assertCookie('XSRF-TOKEN');
});
