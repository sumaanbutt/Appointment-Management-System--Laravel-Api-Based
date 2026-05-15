<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserShiftSchedule\CreateUserShiftScheduleRequest;
use App\Http\Requests\UserShiftSchedule\UpdateUserShiftScheduleRequest;
use App\Models\UserShiftSchedule;
use Illuminate\Http\Request;

class UserShiftScheduleController extends Controller
{
    public function index()
    {
        try{
            $users_shift = UserShiftSchedule::latest()->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'User Schedule Shifts fetched successfully',
                'data' => $users_shift,
            ],200);

        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Shifts',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function store(CreateUserShiftScheduleRequest $request)
    {
        //dd($request->working_days);

        $data = $request->validated();
        try{
            $schedules = [];

            foreach ($request->working_days as $day) {

                $schedule = UserShiftSchedule::create([
                    'business_code' => $request->business_code,
                    'user_code' => $request->user_code,
                    'location_code' => $request->location_code,
                    'employee_type' => $request->employee_type,
                    'working_day' => $day,
                    'shift_start_time' => $request->shift_start_time,
                    'shift_end_time' => $request->shift_end_time,
                ]);

                $schedules[] = $schedule;
            }

            return response()->json([
                'success' => true,
                'message' => 'User Shift scheduled successfully',
                'data' => $schedule
            ], 201);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Shifts',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function show(UserShiftSchedule $userShiftSchedule)
    {
        try{
            if (!$userShiftSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Shift not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User Shift fetched successfully',
                'data' => $userShiftSchedule,
            ],200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Shifts',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function update(UpdateUserShiftScheduleRequest $request, UserShiftSchedule $userShiftSchedule)
    {
        $data = $request->validated();
        try{
            if (!$userShiftSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Shift not found',
                ], 404);
            }

            $userShiftSchedule->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User Shift schedule updated successfully',
                'data' => $userShiftSchedule
            ],201);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Shifts',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function destroy(UserShiftSchedule $userShiftSchedule)
    {
        try{
        if (!$userShiftSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'User Shift not found',
            ], 404);
        }

            $userShiftSchedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'User Shift deleted successfully',
            'data' => $userShiftSchedule
        ],201);

    }catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'User Shift deletion failed',
            'error' => $e->getMessage(),
        ],401);
    }
    }
}
