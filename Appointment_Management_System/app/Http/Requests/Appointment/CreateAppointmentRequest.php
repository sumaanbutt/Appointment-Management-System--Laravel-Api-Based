<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
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

            'location_code' => 'nullable|exists:business_locations,code',

            'client_code' => 'nullable|exists:clients,code',

            'service_code' => 'required|exists:services,code',

            'appointment_start_date' => 'required|date',
            'appointment_end_date' => 'required|date',

            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',

            'notes' => 'nullable|string|max:5000',
        ];
    }
}
