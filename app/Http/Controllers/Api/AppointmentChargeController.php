<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppointmentCharges;
use Illuminate\Http\Request;

class AppointmentChargeController extends Controller
{
    public function index()
    {
        try {

            $data = AppointmentCharges::latest()->paginate(10);

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

            $data = AppointmentCharges::create(
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

    public function show(AppointmentCharges $appointmentCharges)
    {
        try {

            return response()->json([
                'success' => true,
                'data' => $appointmentCharges,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, AppointmentCharges $appointmentCharges)
    {
        try {

            $appointmentCharges->update(
                $request->all()
            );

            $appointmentCharges->refresh();

            return response()->json([
                'success' => true,
                'data' => $appointmentCharges,
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update record',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(AppointmentCharges $appointmentCharges)
    {
        try {

            $appointmentCharges->delete();

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
