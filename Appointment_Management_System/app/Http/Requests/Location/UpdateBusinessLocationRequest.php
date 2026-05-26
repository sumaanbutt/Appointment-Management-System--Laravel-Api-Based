<?php

namespace App\Http\Requests\Location;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessLocationRequest extends FormRequest
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
            'business_code' => 'sometimes',
            'location_name' => 'sometimes',
            'address' => 'sometimes',
            'apartment' => 'sometimes',
            'street' => 'sometimes',
            'city' => 'sometimes',
            'state' => 'sometimes',
            'postal_code' => 'sometimes',
            'country' => 'sometimes',
            'status' => 'sometimes|in:ACTIVE,INACTIVE',
        ];
    }
}
