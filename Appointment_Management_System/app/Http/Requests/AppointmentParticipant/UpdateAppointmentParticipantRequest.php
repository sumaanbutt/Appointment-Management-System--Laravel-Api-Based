<?php

namespace App\Http\Requests\AppointmentParticipant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentParticipantRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'role' => 'nullable|string|max:100',
            'status' => 'nullable|in:ACTIVE,INACTIVE'
        ];
    }
}
