<?php

namespace App\Http\Requests\AppointmentHistory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentHistoryRequest extends FormRequest
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
            'appointment_code' => ['required'],
            'business_code' => ['required'],
            'action' => ['required'],
            'changed_by_code' => ['required'],
            'old_values' => ['required'],
            'new_values' => ['required'],
        ];
    }
}
