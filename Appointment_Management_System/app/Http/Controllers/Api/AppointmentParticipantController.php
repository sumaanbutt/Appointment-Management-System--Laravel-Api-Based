<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentParticipant\CreateAppointmentParticipantRequest;
use App\Http\Requests\AppointmentParticipant\UpdateAppointmentParticipantRequest;
use App\Models\AppointmentParticipant;
use Illuminate\Http\Request;

class AppointmentParticipantController extends Controller
{
    public function store(CreateAppointmentParticipantRequest $request)
    {
        $data = $request->validated();
        try{
            if($data->fails()){
            return response()->json([
                'success' => true,
                'message' => 'Validation errors',
                'errors' => $data->errors(),
            ],401);
        }
            $participant = AppointmentParticipant::create([
                'appointment_code' => $request->appointment_code,
                'business_code' => $request->business_code,
                'user_code' => $request->user_code,
                'user_role' => $request->role,
                'user_type' => $request->user_type,
                'status' => $request->status ?? 'ACTIVE',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Appointment Participant Created',
                'data' => $participant,
            ],200);

            } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Appointment Participant Creation Failed.',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function update(UpdateAppointmentParticipantRequest $request, AppointmentParticipant $appointmentParticipant)
    {
        $data = $request->validated();
        try{
            $participant = AppointmentParticipant::find($appointmentParticipant);

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment Participant not found',
                ], 404);
            }

            if ($data->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $data->errors()
                ], 422);
            }

            $participant->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Appointment Participant Updated',
                'data' => $participant,
            ],200);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Appointment Participant Creation Failed.',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function destroy(AppointmentParticipant $appointmentParticipant)
    {
        try{
            $participant = AppointmentParticipant::find($appointmentParticipant);

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Appointment Participants not found',
                ], 404);
            }

            $participant->delete();
            return response()->json([
                'success' => true,
                'message' => 'Appointment Participants deleted successfully',
            ],201);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Appointment Participants Deletion Failed.',
                'error' => $e->getMessage(),
            ],401);
        }
    }
}
