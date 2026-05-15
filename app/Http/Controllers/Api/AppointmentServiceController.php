<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentService;
use Illuminate\Http\Request;

class AppointmentServiceController extends Controller
{
    public function index()
    {
        try {

            $data = AppointmentService::latest()->paginate(10);

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

            $data = AppointmentService::create(
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

    public function show(AppointmentService $appointmentService)
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $appointmentService,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, AppointmentService $appointmentService)
    {
        try {

            $appointmentService->update(
                $request->all()
            );

            $appointmentService->refresh();

            return response()->json([
                'success' => true,
                'data' => $appointmentService,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(AppointmentService $appointmentService)
    {
        try {

            $appointmentService->delete();

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
