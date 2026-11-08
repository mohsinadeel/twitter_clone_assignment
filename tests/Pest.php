<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Configuration
|--------------------------------------------------------------------------
|
| Configure Pest for Laravel testing with proper traits and expectations.
|
*/

// Global traits for all tests
uses(DatabaseTransactions::class, WithFaker::class);

// Feature tests configuration
pest()->extend(TestCase::class)
    ->use(DatabaseTransactions::class, WithFaker::class)
    ->in('Feature');

// Unit tests configuration
pest()->extend(TestCase::class)
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

function createUser(array $attributes = []): User
{
    return User::factory()->create($attributes);
}

function createPost(array $attributes = []): Post
{
    return Post::factory()->create($attributes);
}

function createAuthenticatedUser(array $attributes = []): User
{
    $user = createUser($attributes);
    $user->createToken('test-token');

    return $user;
}

function getAuthHeaders(User $user): array
{
    $token = $user->createToken('test-token')->plainTextToken;

    return ['Authorization' => 'Bearer '.$token];
}
