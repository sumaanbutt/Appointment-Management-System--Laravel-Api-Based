<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentHistory;
use App\Models\User;
use App\Models\UserShiftSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $query = Appointment::query();

            if(request()->business_code){

                $query->where(
                    'business_code',
                    request()->business_code
                );
            }

            // ADD THIS
            if(request()->filled('status')){

                $query->where(
                    'status',
                    request()->status
                );
            }

            $appointments =
                $query
                    ->latest()
                    ->paginate(10);

            return response()->json([
                'success'=>true,
                'message'=>'Appointments fetched successfully',
                'data'=>$appointments
            ],200);

        }

        catch(\Exception $e){

            return response()->json([
                'success'=>false,
                'message'=>'Failed to fetch Appointments',
                'error'=>$e->getMessage()
            ],401);

        }
    }
    public function store(CreateAppointmentRequest $request)
    {
        $data = $request->all();
        try{
            $alreadyBooked = Appointment::where(
                'location_code',
                $request->location_code
            )
                ->where(
                    'appointment_start_date',
                    $request->appointment_start_date
                )

                ->where(function ($query) use ($request) {

                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })

                ->whereNotIn('status', [
                    'CANCELLED',
                    'REJECTED'
                ])

                ->exists();

        if ($alreadyBooked) {

            return response()->json([
                'success' => false,
                'message' => 'Appointment slot already booked'
            ], 422);
        }

        $appointment = Appointment::create([

            'business_code' => $request->business_code,
            'location_code' => $request->location_code,
            'client_code' => $request->client_code,
            'service_code' => $request->service_code,
            //'availability_slot_code' => $request->availability_slot_code,
            'appointment_start_date' => $request->appointment_start_date,
            'appointment_end_date'=> $request->appointment_end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status ?? 'PENDING',
            'created_by_code' => auth()->user()->code,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully',
            'data' => $appointment->load([
                'business',
                'location',
                'client',
                'service'
            ])
        ], 201);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Appointment',
                'error' => $e->getMessage()
            ],500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return response()->json([
            'success' => true,
            'data' => $appointment->load([
                'business',
                'location',
                'client',
                'service',
                //'availabilitySlot',
                'createdBy',
                'approvedBy',
                //'canceller',
                //'rescheduledFrom'
            ])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        $data = $request->all();
        try{
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found',
                ], 404);
            }

            $alreadyBooked = Appointment::where(
                'location_code',
                $request->location_code
            )
                ->where(
                    'appointment_start_date',
                    $request->appointment_start_date
                )

                ->where(function ($query) use ($request) {

                    $query->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                })

                ->whereNotIn('status', [
                    'CANCELLED',
                    'REJECTED'
                ])

                ->exists();

            if ($alreadyBooked) {

                return response()->json([
                    'success' => false,
                    'message' => 'Appointment slot already booked'
                ], 422);
            }

            $data = array_filter(
                $data,
                fn($value) => $value !== null && $value !== ''
            );

            $appointment->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Appointment updated successfully',
                'data' => $appointment
            ],201);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Appointment',
                'error' => $e->getMessage()
            ],401);
        }
    }


    public function availability($appointment)
    {
        $appointment = Appointment::where(
            'code',
            $appointment
        )->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ],404);
        }

        $day = Carbon::parse(
            $appointment->appointment_start_date
        )->format('l');

        $availableStaff = UserShiftSchedule::query()

            ->join(
                'users',
                'users.code',
                '=',
                'user_shift_schedules.user_code'
            )

            ->where(
                'users.business_code',
                $appointment->business_code
            )

            ->where(
                'users.user_type',
                'SERVICE_STAFF'
            )

            ->where(
                'user_shift_schedules.working_day',
                strtoupper($day)
            )

            ->where(
                'user_shift_schedules.shift_start_time',
                '<=',
                $appointment->start_time
            )

            ->where(
                'user_shift_schedules.shift_end_time',
                '>=',
                $appointment->end_time
            )

            ->select(
                'users.code as user_code',
                'users.name as user_name',
                'user_shift_schedules.working_day',
                'user_shift_schedules.shift_start_time',
                'user_shift_schedules.shift_end_time'
            )

            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'available_staff' => $availableStaff
            ]
        ]);
    }

    public function approve(Request $request, $appointment)
    {
        $request->validate([
            'staff_code' => 'required'
        ]);

        $appointment = Appointment::where(
            'code',
            $appointment
        )->first();

        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ],404);
        }

        $staff = User::where(
            'code',
            $request->staff_code
        )
            ->where(
                'user_type',
                'SERVICE_STAFF'
            )
            ->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service staff selected'
            ],422);
        }

        $appointment->status = 'APPROVED';

        $appointment->staff_code = $staff->code;

        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Appointment approved successfully',
            'data' => $appointment
        ]);
    }

    public function updateStatus(Request $request, Appointment $appointment){

        $request->validate([
            'status'=>[
                'required',
                'in:PENDING,APPROVED,IN_PROGRESS,COMPLETED,CANCELLED,REJECTED,RESCHEDULED'
            ]
        ]);

        $appointment->update([
            'status'=>$request->status
        ]);

        return response()->json([
            'success'=>true,
            'data'=>$appointment
        ]);
    }

    public function history($appointment)
    {
            return AppointmentHistory::where(
            'code',
            $appointment
        )->get();
    }


    public function reschedule(Request $request, Appointment $appointment)
    {
        $appointment->update([
            'appointment_start_date' => $request->appointment_start_date,
            'appointment_end_date' => $request->appointment_end_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'RESCHEDULED'
        ]);

        return response()->json([
            'success'=>true,
            'data'=>$appointment
        ]);
    }


    public function destroy(Appointment $appointment)
    {
        try {
            if (!$appointment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment not found',
                ], 404);
            }

            $appointment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Appointment deleted successfully',
            ],201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment deletion failed',
                'error' => $e->getMessage()
            ],401);
        }
    }
}
