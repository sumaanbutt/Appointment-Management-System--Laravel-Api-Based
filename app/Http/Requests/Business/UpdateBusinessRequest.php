<?php

namespace App\Http\Requests\Business;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessRequest extends FormRequest
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
            //'owner_code' => 'required|exists:users,code',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:businesses,email,',
            'phone' => 'sometimes|string|max:20',
            'description' => 'nullable|string',
            'status' => 'sometimes|in:ACTIVE,INACTIVE',
        ];
    }
}
