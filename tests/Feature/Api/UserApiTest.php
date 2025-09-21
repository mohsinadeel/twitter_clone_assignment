<?php

use App\Models\Post;
use App\Models\User;

describe('User API Endpoints', function () {
    describe('GET /api/v1/users', function () {
        it('returns all users with pagination', function () {
            User::factory()->count(5)->create();

            $response = $this->getJson('/api/v1/users');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'name',
                                'email',
                                'username',
                                'avatar',
                                'created_at',
                                'updated_at',
                                'posts',
                            ],
                        ],
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);
        });

        it('respects per_page parameter', function () {
            User::factory()->count(10)->create();

            $response = $this->getJson('/api/v1/users?per_page=3');

            $response->assertStatus(200);
            $data = $response->json('data');
            expect($data['per_page'])->toBe(3);
            expect($data['data'])->toHaveCount(3);
        });

        it('includes posts relationship', function () {
            $user = User::factory()->create();
            Post::factory()->count(2)->create(['user_id' => $user->id]);

            $response = $this->getJson('/api/v1/users');

            $response->assertStatus(200);
            $userData = $response->json('data.data.0');
            expect($userData['posts'])->toHaveCount(2);
        });

        it('orders users by created_at desc', function () {
            $user1 = User::factory()->create(['created_at' => now()->subDay()]);
            $user2 = User::factory()->create(['created_at' => now()]);

            $response = $this->getJson('/api/v1/users');

            $response->assertStatus(200);
            $users = $response->json('data.data');
            expect($users[0]['id'])->toBe($user2->id);
            expect($users[1]['id'])->toBe($user1->id);
        });
    });

    describe('GET /api/v1/users/{id}', function () {
        it('returns user by id', function () {
            $user = User::factory()->create();

            $response = $this->getJson("/api/v1/users/{$user->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'username' => $user->username,
                    ],
                ]);
        });

        it('returns 404 for non-existent user', function () {
            $response = $this->getJson('/api/v1/users/999');

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        });

        it('includes posts relationship', function () {
            $user = User::factory()->create();
            Post::factory()->count(3)->create(['user_id' => $user->id]);

            $response = $this->getJson("/api/v1/users/{$user->id}");

            $response->assertStatus(200);
            $userData = $response->json('data');
            expect($userData['posts'])->toHaveCount(3);
        });
    });

    describe('GET /api/v1/users/username/{username}', function () {
        it('returns user by username', function () {
            $user = User::factory()->create(['username' => 'testuser']);

            $response = $this->getJson('/api/v1/users/username/testuser');

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $user->id,
                        'username' => 'testuser',
                    ],
                ]);
        });

        it('returns 404 for non-existent username', function () {
            $response = $this->getJson('/api/v1/users/username/nonexistent');

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        });

        it('includes posts relationship', function () {
            $user = User::factory()->create(['username' => 'testuser']);
            Post::factory()->count(2)->create(['user_id' => $user->id]);

            $response = $this->getJson('/api/v1/users/username/testuser');

            $response->assertStatus(200);
            $userData = $response->json('data');
            expect($userData['posts'])->toHaveCount(2);
        });
    });

    describe('PUT /api/v1/users/{id}', function () {
        it('updates user profile with valid data', function () {
            $user = createAuthenticatedUser();

            $updateData = [
                'name' => 'Updated Name',
                'username' => 'updateduser',
                'avatar' => 'https://example.com/avatar.jpg',
            ];

            $response = $this->putJson("/api/v1/users/{$user->id}", $updateData, getAuthHeaders($user));

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User updated successfully',
                ]);

            $user->refresh();
            expect($user->name)->toBe('Updated Name');
            expect($user->username)->toBe('updateduser');
            expect($user->avatar)->toBe('https://example.com/avatar.jpg');
        });

        it('returns 404 for non-existent user', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson('/api/v1/users/999', ['name' => 'Updated Name'], getAuthHeaders($user));

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        });

        it('validates username uniqueness', function () {
            $user1 = User::factory()->create(['username' => 'existinguser']);
            $user2 = createAuthenticatedUser();

            $response = $this->putJson("/api/v1/users/{$user2->id}", [
                'username' => 'existinguser',
            ], getAuthHeaders($user2));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['username']);
        });

        it('allows same user to keep their username', function () {
            $user = createAuthenticatedUser(['username' => 'testuser']);

            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'username' => 'testuser',
            ], getAuthHeaders($user));

            $response->assertStatus(200);
        });

        it('validates name minimum length', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'name' => 'A',
            ], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['name']);
        });

        it('validates username format', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'username' => 'user@name',
            ], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['username']);
        });

        it('validates avatar URL format', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'avatar' => 'not-a-valid-url',
            ], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['avatar']);
        });

        it('allows partial updates', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson("/api/v1/users/{$user->id}", [
                'name' => 'Updated Name Only',
            ], getAuthHeaders($user));

            $response->assertStatus(200);

            $user->refresh();
            expect($user->name)->toBe('Updated Name Only');
        });
    });

    describe('DELETE /api/v1/users/{id}', function () {
        it('deletes user', function () {
            $user = createAuthenticatedUser();

            $response = $this->deleteJson("/api/v1/users/{$user->id}", [], getAuthHeaders($user));

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'User deleted successfully',
                ]);

            expect(User::find($user->id))->toBeNull();
        });

        it('returns 404 for non-existent user', function () {
            $user = createAuthenticatedUser();

            $response = $this->deleteJson('/api/v1/users/999', [], getAuthHeaders($user));

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'User not found',
                ]);
        });

        it('cascades delete to user posts', function () {
            $user = createAuthenticatedUser();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->deleteJson("/api/v1/users/{$user->id}", [], getAuthHeaders($user));

            $response->assertStatus(200);
            expect(Post::find($post->id))->toBeNull();
        });
    });
});
