<?php

namespace App\Http\Requests\UserShiftSchedule;

use Illuminate\Foundation\Http\FormRequest;

class CreateScheduleRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            // Expects a root array of shift entry objects
            '*'                  => ['required', 'array'],
            '*.business_code'    => ['required', 'string', 'exists:businesses,code'],
            '*.user_code'        => ['required', 'string', 'exists:users,code'],
            '*.working_days'     => ['required', 'string', 'in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY'],
            '*.location_code'    => ['nullable', 'string', 'exists:business_locations,code'],
            '*.shift_start_time' => ['required_if:*.status,ACTIVE', 'nullable', 'date_format:H:i'],
            '*.shift_end_time'   => ['required_if:*.status,ACTIVE', 'nullable', 'date_format:H:i'],
            '*.status'           => ['required', 'in:ACTIVE,INACTIVE'],
        ];
    }
}
