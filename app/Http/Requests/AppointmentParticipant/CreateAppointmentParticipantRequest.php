<?php

namespace App\Http\Requests\AppointmentParticipant;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentParticipantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'appointment_code' => 'required|exists:appointments,code',
            'business_code' => 'required|exists:businesses,code',
            'user_code' => 'required|exists:users,code',
            'role' => 'nullable|string|max:100',
            'status' => 'nullable|in:ACTIVE,INACTIVE'
        ];
    }
}
