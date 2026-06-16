<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentRecurrence;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Requests\AppointmentRecurrence\CreateAppointmentRecurrenceRequest;
use App\Http\Requests\AppointmentRecurrence\UpdateAppointmentRecurrenceRequest;
class AppointmentRecurrenceController extends Controller
{
    public function index()
    {
        try {

            $data = AppointmentRecurrence::with('business')
                ->latest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch records',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(CreateAppointmentRecurrenceRequest $request)
    {
//        dd($request->all());

        try {
//            dd($request->all(), $request->status);
            $data = $request->validated();
//            dd($data);

            $appointmentRecurrence = AppointmentRecurrence::create($data);

            return response()->json([
                'success' => true,
                'data' => $appointmentRecurrence,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to create record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(AppointmentRecurrence $appointmentRecurrence)
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $appointmentRecurrence,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(UpdateAppointmentRecurrenceRequest $request, AppointmentRecurrence $appointmentRecurrence)
    {
        try {

            $data = $request->validated();

            $appointmentRecurrence->update($data);

            $appointmentRecurrence->refresh();

            return response()->json([
                'success' => true,
                'data' => $appointmentRecurrence,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(AppointmentRecurrence $appointmentRecurrence)
    {
        try {

            $appointmentRecurrence->delete();

            return response()->json([
                'success' => true,
                'message' => 'Record deleted successfully',
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

//    public function autoReschedule()
//    {
//        $recurrences = AppointmentRecurrence::whereNotNull(
//            'reschedule_after_days'
//        )->get();
//
//        foreach ($recurrences as $recurrence) {
//
//            $appointment = Appointment::where(
//                'code',
//                $recurrence->appointment_code
//            )->first();
//
//            if (!$appointment) {
//                continue;
//            }
//
//            $rescheduleDate = Carbon::parse(
//                $recurrence->created_at
//            )->addDays(
//                $recurrence->reschedule_after_days
//            );
//
//            if (
//                now()->greaterThanOrEqualTo($cancelDate) &&
//                in_array(
//                    $appointment->status,
//                    ['PENDING']
//                )
//            ) {
//                $appointment->update([
//                    'status' => 'RESCHEDULED'
//                ]);
//            }
//        }
//
//        return response()->json([
//            'success' => true,
//            'message' => 'Auto cancel completed'
//        ]);
//    }
}

/*
 public function handle()
{
    $recurrences = AppointmentRecurrence::whereNotNull(
        'reschedule_after_days'
    )->get();

    foreach ($recurrences as $recurrence) {

        $appointment = Appointment::where(
            'code',
            $recurrence->appointment_code
        )->first();

        if (!$appointment) {
            continue;
        }

        $rescheduleDate = Carbon::parse(
            $recurrence->created_at
        )->addDays(
            $recurrence->reschedule_after_days
        );

        if (
            now()->greaterThanOrEqualTo($rescheduleDate) &&
            $appointment->status === 'PENDING'
        ) {

            $startTime = Carbon::parse(
                $appointment->start_date
            )->format('H:i:s');

            $endTime = Carbon::parse(
                $appointment->end_date
            )->format('H:i:s');

            $newStartDate = Carbon::parse(
                $rescheduleDate->toDateString() . ' ' . $startTime
            );

            $newEndDate = Carbon::parse(
                $rescheduleDate->toDateString() . ' ' . $endTime
            );

            $appointment->update([
                'start_date' => $newStartDate,
                'end_date' => $newEndDate,
                'status' => 'RESCHEDULED',
            ]);
        }
    }

    $this->info('Auto reschedule completed.');
}
 */
