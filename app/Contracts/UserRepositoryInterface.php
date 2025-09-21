<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    /**
     * Get all users with pagination.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator;

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User;

    /**
     * Find a user by username.
     */
    public function findByUsername(string $username): ?User;

    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Update a user.
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete a user.
     */
    public function delete(int $id): bool;
}
