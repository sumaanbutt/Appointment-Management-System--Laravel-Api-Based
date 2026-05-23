<?php

namespace App\Http\Requests\UserAbility;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserAbilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ability' => 'nullable|string|max:255',
            'status' => 'nullable|in:ACTIVE,INACTIVE',
        ];
    }
}
