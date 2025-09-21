<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesApiAuthentication
{
    /**
     * Get the authenticated user.
     */
    protected function getAuthenticatedUser(Request $request)
    {
        return $request->user();
    }

    /**
     * Check if user owns the resource.
     */
    protected function userOwnsResource($resource, $userId): bool
    {
        // For User model, compare with id field
        if (isset($resource->id) && !isset($resource->user_id)) {
            return $resource->id === $userId;
        }

        // For other models (like Post), compare with user_id field
        return $resource->user_id === $userId;
    }

    /**
     * Authorize user action on resource.
     */
    protected function authorizeUserAction($resource, $userId, string $action = 'access'): bool
    {
        if (!$this->userOwnsResource($resource, $userId)) {
            return false;
        }

        return true;
    }
}
