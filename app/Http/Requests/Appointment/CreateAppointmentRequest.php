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
            'location_code' => 'required|exists:business_locations,code',
            'client_code' => 'required|exists:clients,code',
            'service_code' => 'required|exists:services,code',
            //'availability_slot_code' => 'nullable|exists:availability_slots,code',
            'appointment_start_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',

            'status' =>
                'nullable|in:
                    PENDING,
                    APPROVED,
                    IN_PROGRESS,
                    COMPLETED,
                    CANCELLED,
                    REJECTED,
                    RESCHEDULED',

            'notes' => 'nullable|string|max:5000',
        ];
    }
}
