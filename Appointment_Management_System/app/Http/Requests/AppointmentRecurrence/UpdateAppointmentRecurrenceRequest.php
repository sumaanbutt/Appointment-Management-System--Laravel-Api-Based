<?php

namespace App\Http\Requests\AppointmentRecurrence;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRecurrenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'recurrence_uom' => [
                'sometimes',
                'in:DAILY,WEEKLY,FORTNIGHTLY,MONTHLY,QUARTERLY,FIXED'
            ],
            'recurrence_value' => ['sometimes'],
            'auto_cancel_after_days' => ['sometimes'],
            'reschedule_after_days' => ['sometimes'],
            'status' => 'sometimes|in:ACTIVE,INACTIVE',
        ];
    }
}
