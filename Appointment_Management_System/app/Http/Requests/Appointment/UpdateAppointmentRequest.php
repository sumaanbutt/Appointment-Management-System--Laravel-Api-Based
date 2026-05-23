<?php

namespace App\Http\Requests\Appointment;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'business_code' => 'sometimes|exists:businesses,code',
            'location_code' => 'sometimes|exists:business_locations,code',
            'client_code' => 'sometimes|exists:clients,code',
            'service_code' => 'sometimes|exists:services,code',
            //'availability_slot_code' => 'nullable|exists:availability_slots,code',
            'appointment_start_date' => 'sometimes|date',
            'appointment_end_date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i',

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
