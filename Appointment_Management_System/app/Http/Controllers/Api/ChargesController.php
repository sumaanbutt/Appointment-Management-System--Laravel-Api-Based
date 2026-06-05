<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Charge\CreateChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{

    public function index()
    {
        try{
            $query = Charge::with('business');

            if(request()->business_code){
                $query->where(
                    'business_code',
                    request()->business_code
                );
            }

            $charge = $query
                ->latest()
                ->paginate(10);

            return response()->json([
                'status' => true,
                'message' => 'charge retrieved successfully',
                'data' => $charge
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to retrieve charge',
            ],401);
        }
    }

    public function store(CreateChargeRequest $request)
    {
        $data = $request->validated();
        try{
            $charge = Charge::create([
                'business_code' => $request->business_code,
                'name' => $request->name,
                'description' => $request->description,
                'charge_uom' => $request->charge_uom,
                'charge_value' => $request->charge_value,
                'status' => 'ACTIVE',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'charge created successfully',
                'data' => $charge
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to create charge',
                'errors' => $e->getMessage()
            ],401);
        }
    }

    public function show(Charge $charge)
    {
        try{
            if(!$charge){
                return response()->json([
                    'status' => false,
                    'message' => 'charge not found',
                    'data' => $charge
                ],404);
            }
            return response()->json([
                'status' => true,
                'message' => 'charge retrieved successfully',
                'data' => $charge
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to retrieve charge',
                'errors' => $e->getMessage()
            ],401);
        }
    }

    public function update(UpdateChargeRequest $request, Charge $charge)
    {
        $data = $request->validated();
        try{
            if(!$charge){
                return response()->json([
                    'status' => false,
                    'message' => 'charge not found',
                    'data' => $charge
                ],404);
            }

            $charge->update($request->validated());

            return response()->json([
                'status' => true,
                'message' => 'charge updated successfully',
                'data' => $charge
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to update charge',
                'errors' => $e->getMessage()
            ],401);
        }
    }

    public function destroy(Charge $charge)
    {
        try{
            if(!$charge){
                return response()->json([
                    'status' => false,
                    'message' => 'charge not found',
                    'data' => $charge
                ],404);
            }

            $charge->delete();
            return response()->json([
                'status' => true,
                'message' => 'charge deleted successfully',
                'data' => $charge
            ],201);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'false',
                'message' => 'Failed to delete charge',
                'errors' => $e->getMessage()
            ],401);
        }
    }
}
