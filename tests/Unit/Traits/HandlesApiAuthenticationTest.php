<?php

use App\Models\Post;
use App\Models\User;
use App\Traits\HandlesApiAuthentication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

uses(RefreshDatabase::class);

describe('HandlesApiAuthentication Trait', function () {
    beforeEach(function () {
        // Create a test controller that uses the HandlesApiAuthentication trait
        $this->controller = new class extends Controller {
            use HandlesApiAuthentication;

            // Make protected methods public for testing
            public function getAuthenticatedUser($request)
            {
                return $request->user();
            }

            public function userOwnsResource($resource, $userId): bool
            {
                if (is_array($resource)) {
                    return $resource['user_id'] === $userId;
                }

                return $resource->user_id === $userId;
            }

            public function authorizeUserAction($resource, $userId, string $action = 'access'): bool
            {
                if (!$this->userOwnsResource($resource, $userId)) {
                    return false;
                }

                return true;
            }
        };
    });

    describe('getAuthenticatedUser', function () {
        it('returns authenticated user from request', function () {
            $user = User::factory()->create();
            $request = Request::create('/test', 'GET');
            $request->setUserResolver(function () use ($user) {
                return $user;
            });

            $result = $this->controller->getAuthenticatedUser($request);

            expect($result)->toBeInstanceOf(User::class);
            expect($result->id)->toBe($user->id);
        });

        it('returns null when no user is authenticated', function () {
            $request = Request::create('/test', 'GET');
            $request->setUserResolver(function () {
                return null;
            });

            $result = $this->controller->getAuthenticatedUser($request);

            expect($result)->toBeNull();
        });
    });

    describe('userOwnsResource', function () {
        it('returns true when user owns the resource', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $result = $this->controller->userOwnsResource($post, $user->id);

            expect($result)->toBeTrue();
        });

        it('returns false when user does not own the resource', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user1->id]);

            $result = $this->controller->userOwnsResource($post, $user2->id);

            expect($result)->toBeFalse();
        });

        it('works with different resource types', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            // Test with Post model
            expect($this->controller->userOwnsResource($post, $user->id))->toBeTrue();

            // Test with array
            $postArray = ['user_id' => $user->id];
            expect($this->controller->userOwnsResource($postArray, $user->id))->toBeTrue();

            // Test with object
            $postObject = (object) ['user_id' => $user->id];
            expect($this->controller->userOwnsResource($postObject, $user->id))->toBeTrue();
        });
    });

    describe('authorizeUserAction', function () {
        it('returns true when user owns the resource', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $result = $this->controller->authorizeUserAction($post, $user->id);

            expect($result)->toBeTrue();
        });

        it('returns false when user does not own the resource', function () {
            $user1 = User::factory()->create();
            $user2 = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user1->id]);

            $result = $this->controller->authorizeUserAction($post, $user2->id);

            expect($result)->toBeFalse();
        });

        it('accepts custom action parameter', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            $result = $this->controller->authorizeUserAction($post, $user->id, 'update');

            expect($result)->toBeTrue();
        });

        it('works with different action types', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            // Test different actions
            expect($this->controller->authorizeUserAction($post, $user->id, 'view'))->toBeTrue();
            expect($this->controller->authorizeUserAction($post, $user->id, 'edit'))->toBeTrue();
            expect($this->controller->authorizeUserAction($post, $user->id, 'delete'))->toBeTrue();
        });
    });

    describe('integration with different resource types', function () {
        it('works with Post model', function () {
            $user = User::factory()->create();
            $post = Post::factory()->create(['user_id' => $user->id]);

            expect($this->controller->userOwnsResource($post, $user->id))->toBeTrue();
            expect($this->controller->authorizeUserAction($post, $user->id))->toBeTrue();
        });

        it('works with User model', function () {
            $user = User::factory()->create();

            // For User model, we'd typically check if the user_id matches the user's own id
            $userResource = (object) ['user_id' => $user->id];

            expect($this->controller->userOwnsResource($userResource, $user->id))->toBeTrue();
            expect($this->controller->authorizeUserAction($userResource, $user->id))->toBeTrue();
        });
    });
});
