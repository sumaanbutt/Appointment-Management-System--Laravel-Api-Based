<?php

namespace App\Http\Requests\AppointmentRecurrence;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRecurrenceRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'business_code' => ['required'],
            'appointment_code' => ['required'],
            'recurrence_uom' => [
                'required',
                'in:DAILY,WEEKLY,FORTNIGHTLY,MONTHLY,QUARTERLY,FIXED'
            ],
            'recurrence_value' => ['required'],
            'auto_cancel_after_days' => ['required'],
            'reschedule_after_days' => ['required'],
            'status' => 'required|in:ACTIVE,INACTIVE',
            ];
    }
}
