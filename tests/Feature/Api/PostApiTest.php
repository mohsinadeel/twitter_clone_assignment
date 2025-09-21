<?php

use App\Models\Post;
use App\Models\User;

describe('Post API Endpoints', function () {
    describe('GET /api/v1/posts', function () {
        it('returns all posts with pagination', function () {
            Post::factory()->count(5)->create();

            $response = $this->getJson('/api/v1/posts');

            $response->assertStatus(200)
                ->assertJsonStructure([
                    'success',
                    'message',
                    'data' => [
                        'data' => [
                            '*' => [
                                'id',
                                'user_id',
                                'content',
                                'created_at',
                                'updated_at',
                                'user' => [
                                    'id',
                                    'name',
                                    'username',
                                ],
                            ],
                        ],
                        'current_page',
                        'per_page',
                        'total',
                    ],
                ]);
        });

        it('respects per_page parameter', function () {
            Post::factory()->count(10)->create();

            $response = $this->getJson('/api/v1/posts?per_page=3');

            $response->assertStatus(200);
            $data = $response->json('data');
            expect($data['per_page'])->toBe(3);
            expect($data['data'])->toHaveCount(3);
        });

        it('includes user relationship', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->getJson('/api/v1/posts');

            $response->assertStatus(200);
            $postData = $response->json('data.data.0');
            expect($postData['user']['id'])->toBe($user->id);
            expect($postData['user']['name'])->toBe($user->name);
        });

        it('orders posts by created_at desc', function () {
            $post1 = Post::factory()->create(['created_at' => now()->subDay()]);
            $post2 = Post::factory()->create(['created_at' => now()]);

            $response = $this->getJson('/api/v1/posts');

            $response->assertStatus(200);
            $posts = $response->json('data.data');
            expect($posts[0]['id'])->toBe($post2->id);
            expect($posts[1]['id'])->toBe($post1->id);
        });
    });

    describe('GET /api/v1/posts/{id}', function () {
        it('returns post by id', function () {
            $post = Post::factory()->create();

            $response = $this->getJson("/api/v1/posts/{$post->id}");

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'data' => [
                        'id' => $post->id,
                        'content' => $post->content,
                        'user_id' => $post->user_id,
                    ],
                ]);
        });

        it('returns 404 for non-existent post', function () {
            $response = $this->getJson('/api/v1/posts/999');

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Post not found',
                ]);
        });

        it('includes user relationship', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->getJson("/api/v1/posts/{$post->id}");

            $response->assertStatus(200);
            $postData = $response->json('data');
            expect($postData['user']['id'])->toBe($user->id);
        });
    });

    describe('GET /api/v1/users/{userId}/posts', function () {
        it('returns posts for specific user', function () {
            $user = User::factory()->create();
            Post::factory()->count(3)->create(['user_id' => $user->id]);
            Post::factory()->count(2)->create(); // Other user's posts

            $response = $this->getJson("/api/v1/users/{$user->id}/posts");

            $response->assertStatus(200);
            $posts = $response->json('data.data');
            expect($posts)->toHaveCount(3);
            expect(collect($posts)->every(fn($post) => $post['user_id'] === $user->id))->toBeTrue();
        });

        it('respects per_page parameter', function () {
            $user = User::factory()->create();
            Post::factory()->count(10)->create(['user_id' => $user->id]);

            $response = $this->getJson("/api/v1/users/{$user->id}/posts?per_page=3");

            $response->assertStatus(200);
            $data = $response->json('data');
            expect($data['per_page'])->toBe(3);
            expect($data['data'])->toHaveCount(3);
        });

        it('includes user relationship', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->getJson("/api/v1/users/{$user->id}/posts");

            $response->assertStatus(200);
            $postData = $response->json('data.data.0');
            expect($postData['user']['id'])->toBe($user->id);
        });

        it('orders posts by created_at desc', function () {
            $user = User::factory()->create();
            $post1 = Post::factory()->create(['user_id' => $user->id, 'created_at' => now()->subDay()]);
            $post2 = Post::factory()->create(['user_id' => $user->id, 'created_at' => now()]);

            $response = $this->getJson("/api/v1/users/{$user->id}/posts");

            $response->assertStatus(200);
            $posts = $response->json('data.data');
            expect($posts[0]['id'])->toBe($post2->id);
            expect($posts[1]['id'])->toBe($post1->id);
        });
    });

    describe('POST /api/v1/posts', function () {
        it('creates post for authenticated user', function () {
            $user = createAuthenticatedUser();
            $postData = ['content' => 'This is a test post.'];

            $response = $this->postJson('/api/v1/posts', $postData, getAuthHeaders($user));

            $response->assertStatus(201)
                ->assertJson([
                    'success' => true,
                    'message' => 'Post created successfully',
                ]);

            expect(Post::where('content', 'This is a test post.')->exists())->toBeTrue();
        });

        it('returns 401 for unauthenticated user', function () {
            $postData = ['content' => 'This is a test post.'];

            $response = $this->postJson('/api/v1/posts', $postData);

            $response->assertStatus(401);
        });

        it('validates content is required', function () {
            $user = createAuthenticatedUser();

            $response = $this->postJson('/api/v1/posts', [], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
        });

        it('validates content is string', function () {
            $user = createAuthenticatedUser();

            $response = $this->postJson('/api/v1/posts', ['content' => 123], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
        });

        it('validates content character limit', function () {
            $user = createAuthenticatedUser();
            $longContent = str_repeat('a', 281);

            $response = $this->postJson('/api/v1/posts', ['content' => $longContent], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
        });

        it('accepts content at character limit', function () {
            $user = createAuthenticatedUser();
            $content = str_repeat('a', 280);

            $response = $this->postJson('/api/v1/posts', ['content' => $content], getAuthHeaders($user));

            $response->assertStatus(201);
        });

        it('sets user_id automatically', function () {
            $user = createAuthenticatedUser();
            $postData = ['content' => 'This is a test post.'];

            $response = $this->postJson('/api/v1/posts', $postData, getAuthHeaders($user));

            $response->assertStatus(201);
            $post = Post::where('content', 'This is a test post.')->first();
            expect($post->user_id)->toBe($user->id);
        });
    });

    describe('PUT /api/v1/posts/{id}', function () {
        it('updates post for owner', function () {
            $user = createAuthenticatedUser();
            $post = Post::factory()->create(['user_id' => $user->id, 'content' => 'Original content']);

            $response = $this->putJson("/api/v1/posts/{$post->id}", [
                'content' => 'Updated content',
            ], getAuthHeaders($user));

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Post updated successfully',
                ]);

            $post->refresh();
            expect($post->content)->toBe('Updated content');
        });

        it('returns 404 for non-existent post', function () {
            $user = createAuthenticatedUser();

            $response = $this->putJson('/api/v1/posts/999', [
                'content' => 'Updated content',
            ], getAuthHeaders($user));

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Post not found',
                ]);
        });

        it('returns 403 for non-owner', function () {
            $user1 = createAuthenticatedUser();
            $user2 = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user2->id]);

            $response = $this->putJson("/api/v1/posts/{$post->id}", [
                'content' => 'Updated content',
            ], getAuthHeaders($user1));

            $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthorized to update this post',
                ]);
        });

        it('returns 401 for unauthenticated user', function () {
            $post = Post::factory()->create();

            $response = $this->putJson("/api/v1/posts/{$post->id}", [
                'content' => 'Updated content',
            ]);

            $response->assertStatus(401);
        });

        it('validates content is required', function () {
            $user = createAuthenticatedUser();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->putJson("/api/v1/posts/{$post->id}", [], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
        });

        it('validates content character limit', function () {
            $user = createAuthenticatedUser();
            $post = Post::factory()->create(['user_id' => $user->id]);
            $longContent = str_repeat('a', 281);

            $response = $this->putJson("/api/v1/posts/{$post->id}", [
                'content' => $longContent,
            ], getAuthHeaders($user));

            $response->assertStatus(422)
                ->assertJsonValidationErrors(['content']);
        });
    });

    describe('DELETE /api/v1/posts/{id}', function () {
        it('deletes post for owner', function () {
            $user = createAuthenticatedUser();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $response = $this->deleteJson("/api/v1/posts/{$post->id}", [], getAuthHeaders($user));

            $response->assertStatus(200)
                ->assertJson([
                    'success' => true,
                    'message' => 'Post deleted successfully',
                ]);

            expect(Post::find($post->id))->toBeNull();
        });

        it('returns 404 for non-existent post', function () {
            $user = createAuthenticatedUser();

            $response = $this->deleteJson('/api/v1/posts/999', [], getAuthHeaders($user));

            $response->assertStatus(404)
                ->assertJson([
                    'success' => false,
                    'message' => 'Post not found',
                ]);
        });

        it('returns 403 for non-owner', function () {
            $user1 = createAuthenticatedUser();
            $user2 = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user2->id]);

            $response = $this->deleteJson("/api/v1/posts/{$post->id}", [], getAuthHeaders($user1));

            $response->assertStatus(403)
                ->assertJson([
                    'success' => false,
                    'message' => 'Unauthorized to delete this post',
                ]);
        });

        it('returns 401 for unauthenticated user', function () {
            $post = Post::factory()->create();

            $response = $this->deleteJson("/api/v1/posts/{$post->id}");

            $response->assertStatus(401);
        });
    });
});
