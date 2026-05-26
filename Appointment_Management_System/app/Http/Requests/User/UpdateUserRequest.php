<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'organization_code' => 'sometimes|exists:organizations,code',
            'business_code' => 'sometimes|exists:businesses,code',
            'name' => 'sometimes|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'password' => 'sometimes|string|min:8',
            'user_type' => 'sometimes|in:ADMIN,BUSINESS_OWNER,OPERATION_STAFF,SERVICE_STAFF,CLIENT',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
