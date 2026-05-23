<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentHistory;
use Illuminate\Http\Request;

class AppointmentHistoryController extends Controller
{
    public function index()
    {
        try {

            $data = AppointmentHistory::latest()->paginate(10);

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

    public function store(Request $request)
    {
        try {

            $data = AppointmentHistory::create(
                $request->all()
            );

            return response()->json([
                'success' => true,
                'data' => $data,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to create record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(AppointmentHistory $appointmentHistory)
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $appointmentHistory,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, AppointmentHistory $appointmentHistory)
    {
        try {

            $appointmentHistory->update(
                $request->all()
            );

            $appointmentHistory->refresh();

            return response()->json([
                'success' => true,
                'data' => $appointmentHistory,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(AppointmentHistory $appointmentHistory)
    {
        try {

            $appointmentHistory->delete();

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
}
