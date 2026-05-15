<?php

namespace App\Http\Requests\LocationServices;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLocationServiceRequest extends FormRequest
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
                'business_code' => ['sometimes', 'exists:businesses,code',],
                'location_code' => ['sometimes', 'exists:business_locations,code',],
                'service_code' => ['sometimes', 'exists:services,code',],
                'status' => ['sometimes',
                    Rule::in([
                        'ACTIVE',
                        'INACTIVE',
                    ]),
                ],
            ];
    }
}
