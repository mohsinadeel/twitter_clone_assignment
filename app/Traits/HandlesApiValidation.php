<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait HandlesApiValidation
{
    /**
     * Validate the request and return validated data.
     */
    protected function validateRequest(Request $request, array $rules): array
    {
        return $request->validate($rules);
    }

    /**
     * Get validated data from a form request.
     */
    protected function getValidatedData($request): array
    {
        return $request->validated();
    }
}
