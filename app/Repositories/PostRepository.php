<?php

namespace App\Repositories;

use App\Contracts\PostRepositoryInterface;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    /**
     * Get all posts with pagination.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get posts by user ID with pagination.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Post::with('user')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post
    {
        return Post::with('user')->find($id);
    }

    /**
     * Create a new post.
     */
    public function create(array $data): Post
    {
        return Post::create($data);
    }

    /**
     * Update a post.
     */
    public function update(int $id, array $data): bool
    {
        $post = Post::find($id);

        if (!$post) {
            return false;
        }

        return $post->update($data);
    }

    /**
     * Delete a post.
     */
    public function delete(int $id): bool
    {
        $post = Post::find($id);

        if (!$post) {
            return false;
        }

        return $post->delete();
    }

    /**
     * Get recent posts with pagination.
     */
    public function getRecent(int $perPage = 15): LengthAwarePaginator
    {
        return Post::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
