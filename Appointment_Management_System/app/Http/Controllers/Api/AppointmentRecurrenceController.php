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

            $data = AppointmentRecurrence::latest()->paginate(10);

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
        try {
            $data = $request->validated();

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

    public function autoCancel()
    {
        $recurrences = AppointmentRecurrence::whereNotNull(
            'auto_cancel_after_days'
        )->get();

        foreach ($recurrences as $recurrence) {

            $appointment = Appointment::where(
                'code',
                $recurrence->appointment_code
            )->first();

            if (!$appointment) {
                continue;
            }

            $cancelDate = Carbon::parse(
                $recurrence->created_at
            )->addDays(
                $recurrence->auto_cancel_after_days
            );

            if (
                now()->greaterThanOrEqualTo($cancelDate) &&
                !in_array(
                    $appointment->status,
                    ['PENDING', 'CANCELLED']
                )
            ) {
                $appointment->update([
                    'status' => 'CANCELLED'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Auto cancel completed'
        ]);
    }
}
