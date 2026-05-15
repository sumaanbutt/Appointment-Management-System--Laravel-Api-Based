<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Appointment\CreateAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
        $appointments = Appointment::with([
            'business',
            'location',
            'client',
            'service',
            //'availabilitySlot',
            'createdBy',
            'approvedBy',
            //'canceller',
            //'rescheduledFrom'
        ])->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Appointments fetched successfully',
            'data' => $appointments
        ]);
        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Appointments',
                'error' => $e->getMessage()
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
            ],401);
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
