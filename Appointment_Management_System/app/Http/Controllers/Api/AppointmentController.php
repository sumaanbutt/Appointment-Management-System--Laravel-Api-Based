<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\AppointmentParticipant;
use App\Models\AppointmentHistory;
use App\Models\Charge;
use App\Models\Invoice;
use App\Models\LocationServices;
use App\Models\Service;
use App\Models\User;
use App\Models\UserShiftSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;


class AppointmentController extends Controller
{

    public function index(Request $request)
    {
        try {
            $query = Appointment::query()
                ->with([
                'business','location','client.user', 'service','createdBy', 'approvedBy',
            ]);

            if (auth()->user()->user_type === 'CLIENT') {
                $query->where(
                    'client_code',
                    auth()->user()->client->code
                );
            }

            if(request()->business_code){

                $query->where(
                    'business_code',
                    request()->business_code
                );
            }

            if(request()->filled('status')){

                $query->where(
                    'status',
                    request()->status
                );
            }

            if ($request->service_code) {
                $query->where(
                    'service_code',
                    $request->service_code
                );
            }

            if ($request->location_code) {
                $query->where(
                    'location_code',
                    $request->location_code
                );
            }

            $appointments = ApiDataHelper::process( $query, $request );

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
                'business_code',
                $request->business_code
            )

                ->where(
                    'location_code',
                    $request->location_code
                )

                ->where(
                    'client_code',
                    $request->client_code
                )

                ->where(
                    'service_code',
                    $request->service_code
                )

                ->where(
                    'appointment_start_date',
                    $request->appointment_start_date
                )

                ->where(function ($query) use ($request) {

                    $query->where(
                        'start_time',
                        '<',
                        $request->end_time
                    )

                        ->where(
                            'end_time',
                            '>',
                            $request->start_time
                        );
                })

                ->whereNotIn(
                    'status',
                    [
                        'CANCELLED',
                        'REJECTED'
                    ]
                )

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
                'user_shift_schedules.location_code',
                $appointment->location_code
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
        $differentTimeSameLocation = [];
        $differentLocationSameTime = [];
        $serviceOtherLocations = [];

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

                ->where(
                    'appointments.location_code',
                    $appointment->location_code
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

                    'conflict_appointment_code' => $conflict->appointment_code,
                    'conflict_start_time' => $conflict->start_time,
                    'conflict_end_time' => $conflict->end_time,
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

        if (empty($availableStaff)) {

            $differentTimeSameLocation = UserShiftSchedule::query()

                ->join(
                    'users',
                    'users.code',
                    '=',
                    'user_shift_schedules.user_code'
                )

                ->where(
                    'user_type',
                    'SERVICE_STAFF'
                )

                ->where(
                    'users.business_code',
                    $appointment->business_code
                )

                ->where(
                    'user_shift_schedules.location_code',
                    $appointment->location_code
                )

                ->where(
                    'user_shift_schedules.working_day',
                    strtoupper($day)
                )

                ->select(
                    'users.code as user_code',
                    'users.name as staff_name',
                    'user_shift_schedules.location_code',
                    'user_shift_schedules.working_day',
                    'user_shift_schedules.shift_start_time as start_time',
                    'user_shift_schedules.shift_end_time as end_time'
                )

                ->get()
                ->toArray();
        }


        $autoApplyCharges = Charge::where(
            'business_code',
            $appointment->business_code
        )
            ->where(
                'status', 'ACTIVE'
            )

            ->where('auto_apply',
                true)

            ->get();


        $optionalCharges = Charge::where(
            'business_code',
            $appointment->business_code
        )
            ->where(
                'status', 'ACTIVE'
            )

            ->where(
                'auto_apply',
                false
            )

            ->get();




        if (empty($availableStaff)) {

            $differentLocationSameTime = UserShiftSchedule::query()

                ->join(
                    'users',
                    'users.code',
                    '=',
                    'user_shift_schedules.user_code'
                )

                ->where(
                    'user_type',
                    'SERVICE_STAFF'
                )

                ->where(
                    'users.business_code',
                    $appointment->business_code
                )

                ->where(
                    'user_shift_schedules.location_code',
                    '!=',
                    $appointment->location_code
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
                    'users.name as staff_name',
                    'user_shift_schedules.location_code',
                    'user_shift_schedules.working_day',
                    'user_shift_schedules.shift_start_time as start_time',
                    'user_shift_schedules.shift_end_time as end_time'
                )

                ->get()
                ->groupBy('location_code')
                ->toArray();
        }

        if (
            empty($availableStaff) &&
            empty($engagedStaff)
        ) {

            $serviceOtherLocations = LocationServices::query()

                ->join(
                    'business_locations',
                    'business_locations.code',
                    '=',
                    'location_services.location_code'
                )

                ->where(
                    'location_services.business_code',
                    $appointment->business_code
                )

                ->where(
                    'location_services.service_code',
                    $appointment->service_code
                )

                ->where(
                    'location_services.location_code',
                    '!=',
                    $appointment->location_code
                )

                ->where(
                    'location_services.availability',
                    'AVAILABLE'
                )

                ->select(
                    'location_services.location_code',
                    'business_locations.address',
                    'business_locations.city'
                )

                ->get()
                ->toArray();
        }

        return response()->json([
            'success' => true,
            'data' => [

                'appointment_code' =>
                    $appointment->code,

                'date' =>
                    $appointment->appointment_start_date,

                'start_time' =>
                    $appointment->start_time,

                'end_time' =>
                    $appointment->end_time,

                'location_code' =>
                    $appointment->location_code,

                'working_day' =>
                    strtolower($day),

                'location_slot_already_booked' =>
                    false,

                'conflicting_appointments' =>
                    [],

                'available_staff' =>
                    $availableStaff,

                'engaged_staff' =>
                    $engagedStaff,

                'charges' => [
                    'auto_apply' => $autoApplyCharges,
                    'optional' => $optionalCharges,
                ],

                'alternatives' => [

                    'different_time_same_location' =>
                        $differentTimeSameLocation,

                    'different_location_same_time' =>
                        $differentLocationSameTime,

                    'selected_service_other_locations' =>
                        $serviceOtherLocations
                ]
            ]
        ]);
//        dd([
//            'appointment' => $appointment->code,
//            'availableStaff' => $availableStaff,
//            'engagedStaff' => $engagedStaff,
//        ]);
    }




    public function approve(Request $request, $appointment)
    {

//        dd([
//            'headers' => $request->headers->all(),
//            'content' => $request->getContent(),
//            'all' => $request->all(),
//            'appointment' => $appointment,
//        ]);

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

//        dd([
//            'appointment_code' => $appointment->code,
//            'appointment_location' => $appointment->location_code,
//            'staff_code' => $staff?->code,
//            'staff_locations' => UserShiftSchedule::where(
//                'user_code',
//                $staff?->code
//            )->pluck('location_code')
//        ]);

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid service staff selected'
            ],422);
        }

        $staffScheduledAtLocation = UserShiftSchedule::where(
            'user_code',
            $staff->code
        )
            ->where(
                'location_code',
                $appointment->location_code
            )
            ->exists();

        if (!$staffScheduledAtLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Staff is not assigned to this location'
            ],422);
        }

        $appointment->status = 'APPROVED';
        $appointment->approved_by_code = auth()->user()->code;
        $appointment->save();

        $service = Service::where(
            'code',
            $appointment->service_code
        )->first();

        $subtotal = $service->charges;
        $total = $subtotal;

        $selectedCharges = Charge::whereIn(
            'code',
            $request->selected_charge_codes ?? []
        )->get();

        foreach ($selectedCharges as $charge) {

            if ($charge->charge_uom === 'PERCENTAGE') {

                $chargeAmount =
                    ($subtotal * $charge->charge_value) / 100;

            } else {

                $chargeAmount =
                    $charge->charge_value;
            }

            $total += $chargeAmount;
        }

        Invoice::create([
            'business_code' => $appointment->business_code,
            'appointment_code' => $appointment->code,
            'subtotal' => $subtotal,
            'total' => $total,
            'status' => 'unpaid',
            'invoice_date' => now()->toDateString(),
        ]);

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
                    'appointments.location_code',
                    $appointment->location_code
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

    public function rescheduleRespond(
        Request $request,
        Appointment $appointment
    )
    {
        $request->validate([
            'action' => 'required|in:accepted,rejected'
        ]);

        if ($appointment->status !== 'RESCHEDULED') {

            return response()->json([
                'success' => false,
                'message' => 'Appointment is not awaiting reschedule response'
            ], 422);
        }

        if ($request->action === 'accepted') {

            $appointment->update([
                'status' => 'PENDING'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reschedule accepted',
                'data' => $appointment
            ]);
        }

        $appointment->update([
            'status' => 'CANCELLED'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Reschedule rejected and appointment cancelled',
            'data' => $appointment
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
