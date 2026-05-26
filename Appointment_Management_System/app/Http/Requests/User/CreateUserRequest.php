<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'business_code' => ['nullable', 'exists:businesses,code',
                /*function ($attribute, $value, $fail) {
                    if (
                        in_array($this->user_type, [
                            'BUSINESS_OWNER',
                            'OPERATION_STAFF',
                            'SERVICE_STAFF'
                        ]) && empty($value)
                    ) {
                        $fail('Business code is required for this user type.');
                    }
                }*/
            ],
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|confirmed|min:8',
            'user_type' => 'required|in:SUPER_ADMIN,BUSINESS_OWNER,OPERATION_STAFF,SERVICE_STAFF,CLIENT',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
