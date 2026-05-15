<?php

namespace App\Http\Requests\AppointmentRecurrence;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRecurrenceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'business_code' => ['required'],
            'service_code' => ['required'],
            'location_code' => ['required'],
            'recurrence_uom' => ['required'],
            'recurrence_value' => ['required']
        ];
    }
}
