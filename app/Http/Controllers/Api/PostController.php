<?php

namespace App\Http\Controllers\Api;

use App\Contracts\PostRepositoryInterface;
use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\GetUserPostsRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Traits\ApiResponse;
use App\Traits\HandlesApiAuthentication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostController extends Controller
{
    use ApiResponse, HandlesApiAuthentication;

    public function __construct(
        private PostRepositoryInterface $postRepository
    ) {}

    /**
     * Get all posts.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $posts = $this->postRepository->getAll($perPage);

        return $this->successResponse($posts, 'Posts retrieved successfully');
    }

    /**
     * Get posts by user ID.
     */
    public function getByUserId(GetUserPostsRequest $request, int $userId): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $posts = $this->postRepository->getByUserId($userId, $perPage);

        return $this->successResponse($posts, 'User posts retrieved successfully');
    }

    /**
     * Get a specific post by ID.
     */
    public function show(int $id): JsonResponse
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        return $this->successResponse($post, 'Post retrieved successfully');
    }

    /**
     * Create a new post.
     */
    public function store(CreatePostRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $this->getAuthenticatedUser($request);
        $data['user_id'] = $user->id;

        $post = $this->postRepository->create($data);

        return $this->successResponse($post, 'Post created successfully', 201);
    }

    /**
     * Update a post.
     */
    public function update(UpdatePostRequest $request, int $id): JsonResponse
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        $user = $this->getAuthenticatedUser($request);

        // Check if the user owns the post
        if (!$this->userOwnsResource($post, $user->id)) {
            return $this->errorResponse('Unauthorized to update this post', 403);
        }

        $data = $request->validated();
        $updated = $this->postRepository->update($id, $data);

        if (!$updated) {
            return $this->errorResponse('Failed to update post', 500);
        }

        $post = $this->postRepository->findById($id);

        return $this->successResponse($post, 'Post updated successfully');
    }

    /**
     * Delete a post.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = $this->postRepository->findById($id);

        if (!$post) {
            return $this->notFoundResponse('Post not found');
        }

        $user = $this->getAuthenticatedUser($request);

        // Check if the user owns the post
        if (!$this->userOwnsResource($post, $user->id)) {
            return $this->errorResponse('Unauthorized to delete this post', 403);
        }

        $deleted = $this->postRepository->delete($id);

        if (!$deleted) {
            return $this->errorResponse('Failed to delete post', 500);
        }

        return $this->successResponse(null, 'Post deleted successfully');
    }
}
