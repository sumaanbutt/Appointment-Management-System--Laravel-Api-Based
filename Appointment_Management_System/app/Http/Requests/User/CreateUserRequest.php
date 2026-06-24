<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        // Force null if NOT operation or service staff
        if (!in_array($this->user_type, ['OPERATION_STAFF', 'SERVICE_STAFF'])) {
            $this->merge(['employee_type' => null]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'organization_code' => 'nullable',
            'business_code' => ['nullable', 'exists:businesses,code'],

            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'unique:users,email'],
            'password'      => ['required', 'string', 'confirmed', 'min:8'],
            'user_type'     => ['required', 'in:SUPER_ADMIN,BUSINESS_OWNER,OPERATION_STAFF,SERVICE_STAFF,CLIENT'],
            'employee_type' => [
                'required_if:user_type,OPERATION_STAFF,SERVICE_STAFF',
                'nullable',
                'in:PERMANENT,VISITING,REMOTE'
            ],
            'phone'         => ['nullable', 'string', 'max:255'],
            'status'        => ['nullable', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
