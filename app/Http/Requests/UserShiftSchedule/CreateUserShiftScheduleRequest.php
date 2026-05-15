<?php

namespace App\Http\Requests\UserShiftSchedule;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserShiftScheduleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'business_code' => 'required|exists:businesses,code',
            'user_code' => 'required|exists:users,code',
            'location_code' => 'required|exists:business_locations,code',
            'employee_type' => 'required|in:PERMANENT,VISITING,REMOTE',
            'working_days' => 'required|array',
            'working_days.*' => 'required|
                                in:MONDAY,TUESDAY,WEDNESDAY,THURSDAY,FRIDAY,SATURDAY,SUNDAY',
            'shift_start_time' => 'required|date_format:H:i',
            'shift_end_time' => 'required|date_format:H:i|',
        ];
    }
}
