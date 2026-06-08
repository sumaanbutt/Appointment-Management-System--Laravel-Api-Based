<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentParticipant;
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

            $query = Appointment::with([
                'business','location','client.user', 'service','createdBy', 'approvedBy',
            ]);

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
            ], 404);
        }

        $day = Carbon::parse(
            $appointment->appointment_start_date
        )->format('l');

        // Staff scheduled for this day and time
        $scheduledStaff = UserShiftSchedule::query()

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
                strtolower($day)
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

        $availableStaff = [];
        $engagedStaff = [];

        foreach ($scheduledStaff as $staff) {

            $conflict = AppointmentParticipant::query()

                ->join(
                    'appointments',
                    'appointments.code',
                    '=',
                    'appointment_participants.appointment_code'
                )

                ->where(
                    'appointment_participants.user_code',
                    $staff->user_code
                )

                ->where(
                    'appointment_participants.status',
                    'ACTIVE'
                )

                ->whereDate(
                    'appointments.appointment_start_date',
                    $appointment->appointment_start_date
                )

                ->whereIn(
                    'appointments.status',
                    [
                        'APPROVED',
                        'IN_PROGRESS'
                    ]
                )

                ->where(
                    'appointments.code',
                    '!=',
                    $appointment->code
                )

                ->where(function ($query) use ($appointment) {

                    $query
                        ->where(
                            'appointments.start_time',
                            '<',
                            $appointment->end_time
                        )

                        ->where(
                            'appointments.end_time',
                            '>',
                            $appointment->start_time
                        );
                })

                ->select(
                    'appointments.code as appointment_code',
                    'appointments.start_time',
                    'appointments.end_time'
                )

                ->first();

            if ($conflict) {

                $engagedStaff[] = [
                    'user_code' => $staff->user_code,
                    'user_name' => $staff->user_name,
                    'working_day' => $staff->working_day,
                    'shift_start_time' => $staff->shift_start_time,
                    'shift_end_time' => $staff->shift_end_time,

                    'conflict_appointment_code' =>
                        $conflict->appointment_code,

                    'conflict_start_time' =>
                        $conflict->start_time,

                    'conflict_end_time' =>
                        $conflict->end_time,
                ];

            } else {

                $availableStaff[] = [
                    'user_code' => $staff->user_code,
                    'user_name' => $staff->user_name,
                    'working_day' => $staff->working_day,
                    'shift_start_time' => $staff->shift_start_time,
                    'shift_end_time' => $staff->shift_end_time,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'available_staff' => $availableStaff,
                'engaged_staff' => $engagedStaff,
            ]
        ]);
    }


//    public function availability($appointment)
//    {
//        $appointment = Appointment::where(
//            'code',
//            $appointment
//        )->first();
//
//        if (!$appointment) {
//            return response()->json([
//                'success' => false,
//                'message' => 'Appointment not found'
//            ],404);
//        }
//
//        $day = Carbon::parse(
//            $appointment->appointment_start_date
//        )->format('l');
//
//        $availableStaff = UserShiftSchedule::query()
//
//            ->join(
//                'users',
//                'users.code',
//                '=',
//                'user_shift_schedules.user_code'
//            )
//
//            ->where(
//                'users.business_code',
//                $appointment->business_code
//            )
//
//            ->where(
//                'users.user_type',
//                'SERVICE_STAFF'
//            )
//
//            ->where(
//                'user_shift_schedules.working_day',
//                strtolower($day)
//            )
//
//            ->where(
//                'user_shift_schedules.shift_start_time',
//                '<=',
//                $appointment->start_time
//            )
//
//            ->where(
//                'user_shift_schedules.shift_end_time',
//                '>=',
//                $appointment->end_time
//            )
//
//            ->select(
//                'users.code as user_code',
//                'users.name as user_name',
//                'user_shift_schedules.working_day',
//                'user_shift_schedules.shift_start_time',
//                'user_shift_schedules.shift_end_time'
//            )
//
//            ->get();
//
//        return response()->json([
//            'success' => true,
//            'data' => [
//                'available_staff' => $availableStaff
//            ]
//        ]);
//    }

    public function approve(Request $request, $appointment)
    {
        $request->validate([
            'staff_code' => 'required',
            'force_reassign' => 'nullable|boolean',
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
        $appointment->approved_by_code = auth()->user()->code;
        $appointment->save();

        $existingParticipant = AppointmentParticipant::where(
            'appointment_code',
            $appointment->code
        )
            ->where(
                'user_code',
                $staff->code
            )
            ->where(
                'status',
                'ACTIVE'
            )
            ->first();

        if (!$existingParticipant) {

            if ($request->force_reassign) {

                $activeAssignments = AppointmentParticipant::where(
                    'user_code',
                    $staff->code
                )
                    ->where(
                        'status',
                        'ACTIVE'
                    )
                    ->get();

                foreach ($activeAssignments as $assignment) {

                    // Old appointment becomes pending
                    Appointment::where(
                        'code',
                        $assignment->appointment_code
                    )->update([
                        'status' => 'PENDING',
                        'approved_by_code' => null,
                    ]);

                    // Staff removed from old appointment
                    $assignment->update([
                        'status' => 'INACTIVE'
                    ]);
                }
            }

            $conflictingAssignments = AppointmentParticipant::query()

                ->join(
                    'appointments',
                    'appointments.code',
                    '=',
                    'appointment_participants.appointment_code'
                )

                ->where(
                    'appointment_participants.user_code',
                    $staff->code
                )

                ->where(
                    'appointment_participants.status',
                    'ACTIVE'
                )

                ->where(
                    'appointments.code',
                    '!=',
                    $appointment->code
                )

                ->whereDate(
                    'appointments.appointment_start_date',
                    $appointment->appointment_start_date
                )

                ->where(
                    'appointments.start_time',
                    '<',
                    $appointment->end_time
                )

                ->where(
                    'appointments.end_time',
                    '>',
                    $appointment->start_time
                )

                ->select(
                    'appointment_participants.id',
                    'appointment_participants.appointment_code'
                )

                ->get();

            if ($request->force_reassign) {

                foreach ($conflictingAssignments as $conflict) {

                    Appointment::where(
                        'code',
                        $conflict->appointment_code
                    )->update([
                        'status' => 'PENDING',
                        'approved_by_code' => null,
                    ]);

                    AppointmentParticipant::where(
                        'id',
                        $conflict->id
                    )->update([
                        'status' => 'INACTIVE'
                    ]);
                }
            }

        AppointmentParticipant::create([
            'appointment_code' => $appointment->code,
            'business_code'    => $appointment->business_code,
            'user_code'        => $staff->code,
            'user_role'        => strtolower($staff->user_type),
            'user_type'        => strtolower(auth()->user()->user_type),
            'status'           => 'ACTIVE',
        ]);
        }
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
