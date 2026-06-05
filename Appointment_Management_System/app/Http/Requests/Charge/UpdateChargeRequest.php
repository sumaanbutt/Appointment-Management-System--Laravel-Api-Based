<?php

namespace App\Http\Requests\Charge;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChargeRequest extends FormRequest
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
            'business_code' => 'nullable|exists:businesses,code',
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'charge_uom' => 'nullable|in:FIXED,PERCENTAGE',
            'charge_value' => 'nullable|numeric|min:0',
            'status' => 'sometimes|in:ACTIVE,INACTIVE',
        ];
    }
}
