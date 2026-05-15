<?php

namespace App\Http\Requests\Service;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
            'location_code' => 'required|exists:business_locations,code',
            'service_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'time_duration' => 'nullable|integer',
            'charges' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'currency' => 'nullable|string|max:10',
            'availability' => 'required|in:ONSITE,ONLINE,BOTH',
            'duration_uom' => 'nullable|in:WEEK,DAY,HOUR,MINUTE',
        ];
    }
}
