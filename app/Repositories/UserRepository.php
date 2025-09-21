<?php

namespace App\Repositories;

use App\Contracts\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Get all users with pagination.
     */
    public function getAll(int $perPage = 15): LengthAwarePaginator
    {
        return User::with('posts')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find a user by ID.
     */
    public function findById(int $id): ?User
    {
        return User::with('posts')->find($id);
    }

    /**
     * Find a user by username.
     */
    public function findByUsername(string $username): ?User
    {
        return User::with('posts')
            ->where('username', $username)
            ->first();
    }

    /**
     * Create a new user.
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * Update a user.
     */
    public function update(int $id, array $data): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        return $user->update($data);
    }

    /**
     * Delete a user.
     */
    public function delete(int $id): bool
    {
        $user = User::find($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }
}
