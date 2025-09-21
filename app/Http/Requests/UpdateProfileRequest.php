<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'name' => 'sometimes|string|max:255|min:2',
            'username' => [
                'sometimes',
                'string',
                'max:255',
                'min:3',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($userId)
            ],
            'avatar' => 'sometimes|nullable|string|max:500|url',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot exceed 255 characters.',
            'name.min' => 'Name must be at least 2 characters.',
            'username.string' => 'Username must be a string.',
            'username.max' => 'Username cannot exceed 255 characters.',
            'username.min' => 'Username must be at least 3 characters.',
            'username.alpha_dash' => 'Username can only contain letters, numbers, dashes and underscores.',
            'username.unique' => 'This username is already taken.',
            'avatar.string' => 'Avatar must be a string.',
            'avatar.max' => 'Avatar URL cannot exceed 500 characters.',
            'avatar.url' => 'Avatar must be a valid URL.',
        ];
    }
}
