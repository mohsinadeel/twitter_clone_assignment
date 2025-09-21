<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

/*
|--------------------------------------------------------------------------
| Test Configuration
|--------------------------------------------------------------------------
|
| Configure Pest for Laravel testing with proper traits and expectations.
|
*/

// Global traits for all tests
uses(RefreshDatabase::class, WithFaker::class);

// Feature tests configuration
pest()->extend(Tests\TestCase::class)
    ->use(RefreshDatabase::class, WithFaker::class)
    ->in('Feature');

// Unit tests configuration
pest()->extend(Tests\TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Custom Expectations
|--------------------------------------------------------------------------
|
| Add custom expectations for better test readability.
|
*/

expect()->extend('toBeSuccessfulResponse', function () {
    return $this->toBeGreaterThanOrEqual(200)
        ->toBeLessThan(300);
});

expect()->extend('toBeValidationError', function () {
    return $this->toBe(422);
});

expect()->extend('toBeNotFound', function () {
    return $this->toBe(404);
});

expect()->extend('toBeUnauthorized', function () {
    return $this->toBe(401);
});

expect()->extend('toBeForbidden', function () {
    return $this->toBe(403);
});

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
|
| Global helper functions for common test operations.
|
*/

function createUser(array $attributes = []): \App\Models\User
{
    return \App\Models\User::factory()->create($attributes);
}

function createPost(array $attributes = []): \App\Models\Post
{
    return \App\Models\Post::factory()->create($attributes);
}

function createAuthenticatedUser(array $attributes = []): \App\Models\User
{
    $user = createUser($attributes);
    $user->createToken('test-token');
    return $user;
}

function getAuthHeaders(\App\Models\User $user): array
{
    $token = $user->createToken('test-token')->plainTextToken;
    return ['Authorization' => 'Bearer ' . $token];
}
