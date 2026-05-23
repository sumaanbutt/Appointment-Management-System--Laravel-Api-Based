<?php

namespace App\Http\Requests\UserAbility;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserAbilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_code' => 'required|exists:businesses,code',
            'user_code' => 'required|exists:users,code',
            'ability' => 'required|string|max:255',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
