<?php

namespace App\Http\Requests\UserShiftSchedule;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserShiftScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'business_code' => 'nullable|exists:businesses,code',
            'user_code' => 'nullable|exists:users,code',
            'location_code' => 'nullable|exists:business_locations,code',
            'employee_type' => 'nullable|in:PERMANENT,VISITING,REMOTE',
            'working_days' => 'nullable|array',
            'working_days.*' => 'nullable|
                                in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
            'shift_start_time' => 'nullable|date_format:H:i',
            'shift_end_time' => 'nullable|date_format:H:i|',
        ];
    }
}
