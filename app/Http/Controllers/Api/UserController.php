<?php

namespace App\Http\Controllers\Api;

use App\Contracts\UserRepositoryInterface;
use App\Http\Requests\UpdateProfileRequest;
use App\Traits\ApiResponse;
use App\Traits\HandlesApiAuthentication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UserController extends Controller
{
    use ApiResponse, HandlesApiAuthentication;

    public function __construct(
        private UserRepositoryInterface $userRepository
    ) {}

    /**
     * Get all users.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $users = $this->userRepository->getAll($perPage);

        return $this->successResponse($users, 'Users retrieved successfully');
    }

    /**
     * Get a specific user by ID.
     */
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse($user, 'User retrieved successfully');
    }

    /**
     * Get a specific user by username.
     */
    public function showByUsername(string $username): JsonResponse
    {
        $user = $this->userRepository->findByUsername($username);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        return $this->successResponse($user, 'User retrieved successfully');
    }

    /**
     * Update a user.
     */
    public function update(UpdateProfileRequest $request, int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $authenticatedUser = $this->getAuthenticatedUser($request);

        // Check if the user is updating their own profile
        if (!$this->userOwnsResource($user, $authenticatedUser->id)) {
            return $this->errorResponse('Unauthorized to update this user', 403);
        }

        $data = $request->validated();
        $updated = $this->userRepository->update($id, $data);

        if (!$updated) {
            return $this->errorResponse('Failed to update user', 500);
        }

        $user = $this->userRepository->findById($id);

        return $this->successResponse($user, 'User updated successfully');
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $this->userRepository->findById($id);

        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        $authenticatedUser = $this->getAuthenticatedUser($request);

        // Check if the user is deleting their own profile
        if (!$this->userOwnsResource($user, $authenticatedUser->id)) {
            return $this->errorResponse('Unauthorized to delete this user', 403);
        }

        $deleted = $this->userRepository->delete($id);

        if (!$deleted) {
            return $this->errorResponse('Failed to delete user', 500);
        }

        return $this->successResponse(null, 'User deleted successfully');
    }
}
