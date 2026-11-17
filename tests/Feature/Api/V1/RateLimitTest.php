<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows authenticated users 300 requests per minute', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson('/api/v1/user')
        ->assertSuccessful()
        ->assertHeader('X-RateLimit-Limit', 300)
        ->assertHeader('X-RateLimit-Remaining', 299);
});

it('limits unauthenticated requests to 60 per minute', function () {
    $this->getJson('/api/v1/user')
        ->assertUnauthorized()
        ->assertHeader('X-RateLimit-Limit', 60)
        ->assertHeader('X-RateLimit-Remaining', 59);
});

it('tracks the limit separately for each user', function () {
    Sanctum::actingAs(User::factory()->create());

    $this->getJson('/api/v1/user')->assertHeader('X-RateLimit-Remaining', 299);
    $this->getJson('/api/v1/user')->assertHeader('X-RateLimit-Remaining', 298);

    Sanctum::actingAs(User::factory()->create());

    $this->getJson('/api/v1/user')->assertHeader('X-RateLimit-Remaining', 299);
});

it('rejects authenticated requests once the limit is exceeded', function () {
    Sanctum::actingAs(User::factory()->create());

    foreach (range(1, 300) as $attempt) {
        $this->getJson('/api/v1/user')->assertSuccessful();
    }

    $this->getJson('/api/v1/user')->assertTooManyRequests();
});
