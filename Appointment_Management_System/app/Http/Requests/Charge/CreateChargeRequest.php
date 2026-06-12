<?php

namespace App\Http\Requests\Charge;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateChargeRequest extends FormRequest
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
            'business_code' => 'required|exists:businesses,code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'charge_uom' => 'required|in:FIXED,PERCENTAGE',
            'charge_value' => 'required|numeric|min:0',
            'auto_apply' => 'required|boolean',
        ];
    }
}
