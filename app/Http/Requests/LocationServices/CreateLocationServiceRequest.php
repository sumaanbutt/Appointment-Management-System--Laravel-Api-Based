<?php

namespace App\Http\Requests\LocationServices;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateLocationServiceRequest extends FormRequest
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
            'business_code' => ['required', 'exists:businesses,code',],
            'location_code' => ['required', 'exists:business_locations,code',],
            'service_code' => ['required', 'exists:services,code',
                Rule::unique('location_services')
                    ->where(function ($query) {
                        return $query->where(
                            'location_code',
                            $this->location_code
                        );
                    }),
            ],
            'availability' => ['required',
                Rule::in([
                    'AVAILABLE',
                    'NOT_AVAILABLE',
                ]),
            ],
        ];
    }
}
