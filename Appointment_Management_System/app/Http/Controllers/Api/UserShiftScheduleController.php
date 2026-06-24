<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserShiftSchedule\CreateScheduleRequest;
use App\Http\Requests\UserShiftSchedule\UpdateScheduleRequest;
use App\Models\UserShiftSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserShiftScheduleController extends Controller
{
    public function index(Request $request)
    {
    try {
        $query = UserShiftSchedule::query()
            ->with([
                'business',
                'user',
                'location']);

        if ($request->business_code) {
            $query->where(
                'business_code',
                $request->business_code
            );
        }

        if ($request->user_code) {
            $query->where(
                'user_code',
                $request->user_code
            );
        }

        if ($request->location_code) {
            $query->where(
                'location_code',
                $request->location_code
            );
        }

        $usersShift = ApiDataHelper::process( $query, $request );

        return response()->json([
            'success' => true,
            'message' => 'User Schedule Shifts fetched successfully',
            'data' => $usersShift,
        ], 200);

        } catch (\Exception $e) {

        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch User Shifts',
            'error' => $e->getMessage(),
        ], 500);

    }
}

    public function store(CreateScheduleRequest $request)
    {
        $payload = $request->json()->all();

        DB::beginTransaction();
        try {
            $recordsToInsert = [];
            $now = now();

            foreach ($payload as $row) {
                $dayInput = $row['working_days'] ?? $row['working_day'] ?? 'MONDAY';

                $exists = UserShiftSchedule::where(
                    'business_code',
                    $row['business_code']
                )
                    ->where(
                        'user_code',
                        $row['user_code']
                    )
                    ->where(
                        'location_code',
                        $row['location_code'] ?? null
                    )
                    ->where(
                        'working_day',
                        strtolower($dayInput)
                    )
                    ->where(
                        'shift_start_time',
                        $row['shift_start_time']
                    )
                    ->where(
                        'shift_end_time',
                        $row['shift_end_time']
                    )
                    ->exists();

                if ($exists) {
                    continue;
                }

                $recordsToInsert[] = [
                    'code'             => 'SCH' . strtoupper(Str::random(6)),
                    'business_code'    => $row['business_code'],
                    'user_code'        => $row['user_code'],
                    'working_day'      => strtolower($dayInput),
                    'location_code'    => $row['location_code'] ?? null,
                    'shift_start_time' => $row['shift_start_time'],
                    'shift_end_time'   => $row['shift_end_time'],
                    'status'           => 'ACTIVE',
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            UserShiftSchedule::insert($recordsToInsert);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Weekly schedule matrix created successfully.',
                'count'   => count($recordsToInsert)
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to compile bulk weekly schedules.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(UserShiftSchedule $userShiftSchedule)
    {
        try {
            $userShiftSchedule->load('user');

            return response()->json([
                'success' => true,
                'message' => 'User Shift fetched successfully',
                'data' => $userShiftSchedule,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Shift',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateScheduleRequest $request, UserShiftSchedule $userShiftSchedule)
    {
        try {
            $validated = $request->validated();

            if (isset($validated['status']) && strtoupper($validated['status']) === 'INACTIVE') {
                $validated['shift_start_time'] = '00:00:00';
                $validated['shift_end_time'] = '00:00:00';
            }

            $userShiftSchedule->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'User Shift schedule updated successfully',
                'data' => $userShiftSchedule->load('user')
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update User Shift',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(UserShiftSchedule $userShiftSchedule)
    {
        try {
            $userShiftSchedule->delete();

            return response()->json([
                'success' => true,
                'message' => 'User Shift deleted successfully',
                'data' => $userShiftSchedule
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Shift deletion failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkStaffAvailability(Request $request)
    {
        try {
            $query = UserShiftSchedule::with('user');

            // 1. Strict Target Location Filter
            if ($request->location_code) {
                $query->where('location_code', $request->location_code);
            }

            // 2. ONLY fetch active working schedules (ignore rest days / off days)
            $query->where('status', 'ACTIVE');

            // 3. Dynamic Date Filter (Convert '2026-06-02' -> 'tuesday')
            if ($request->date) {
                try {
                    $dayOfWeek = strtolower(date('l', strtotime($request->date))); // Returns 'monday', 'tuesday', etc.
                    $query->where('working_day', $dayOfWeek);
                } catch (\Exception $e) {
                    return response()->json([
                        'message' => 'Invalid date format provided.'
                    ], 422);
                }
            }

            $schedules = $query->get();
            $available = [];
            $unavailable = [];

            foreach ($schedules as $schedule) {
                $isAvailable = true;

                // 4. Precise Time Window Boundary Check
                if ($request->start_time && $request->end_time) {
                    // Formatting database shift boundaries cleanly to 5-character string (HH:mm) for matching comparisons
                    $dbStart = substr($schedule->shift_start_time, 0, 5);
                    $dbEnd = substr($schedule->shift_end_time, 0, 5);
                    $reqStart = substr($request->start_time, 0, 5);
                    $reqEnd = substr($request->end_time, 0, 5);

                    // The staff member is unavailable if the requested shift starts before they open,
                    // or if the requested shift extends past when they close.
                    if ($reqStart < $dbStart || $reqEnd > $dbEnd) {
                        $isAvailable = false;
                    }
                }

                if ($isAvailable) {
                    $available[] = [
                        'user_code'     => $schedule->user_code,
                        'employee_type' => $schedule->user ? $schedule->user->employee_type : null,
                        'working_days'  => strtoupper($schedule->working_day),
                        'start_time'    => substr($schedule->shift_start_time, 0, 5),
                        'end_time'      => substr($schedule->shift_end_time, 0, 5),
                        'location_code' => $schedule->location_code
                    ];
                } else {
                    $unavailable[] = [
                        'user_code' => $schedule->user_code,
                        'reason'    => 'Outside shift timing window'
                    ];
                }
            }

            return response()->json([
                'available'   => $available,
                'unavailable' => $unavailable
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Availability check failed',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
