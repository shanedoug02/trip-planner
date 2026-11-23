<?php

use Illuminate\Support\Facades\Http;

test('passes when the web server and database are up', function () {
    Http::fake(['*/up' => Http::response('', 200)]);

    $this->artisan('app:status')
        ->expectsOutputToContain('✅ up')
        ->expectsOutputToContain('✅ connected')
        ->assertSuccessful();
});

test('fails when the web server is down', function () {
    Http::fake(['*/up' => Http::failedConnection()]);

    $this->artisan('app:status')
        ->expectsOutputToContain('❌')
        ->assertFailed();
});

test('fails when the web server returns an error', function () {
    Http::fake(['*/up' => Http::response('', 500)]);

    $this->artisan('app:status')
        ->expectsOutputToContain('❌ HTTP 500')
        ->assertFailed();
});
