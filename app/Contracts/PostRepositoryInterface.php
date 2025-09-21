<?php

namespace App\Contracts;

use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;

interface PostRepositoryInterface
{
    /**
     * Get all posts with pagination.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    /**
     * Get posts by user ID with pagination.
     */
    public function getByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a post by ID.
     */
    public function findById(int $id): ?Post;

    /**
     * Create a new post.
     */
    public function create(array $data): Post;

    /**
     * Update a post.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a post.
     */
    public function delete(int $id): bool;

    /**
     * Get recent posts with pagination.
     */
    public function getRecent(int $perPage = 15): LengthAwarePaginator;
}
