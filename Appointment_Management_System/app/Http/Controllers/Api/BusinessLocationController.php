<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Location\CreateBusinessLocationRequest;
use App\Http\Requests\Location\UpdateBusinessLocationRequest;
use Illuminate\Http\Request;
use App\Models\BusinessLocation;
use Illuminate\Support\Facades\Validator;

class BusinessLocationController extends Controller
{
    public function index()
    {
        try{
            $query = BusinessLocation::query();
            if(request()->business_code){
                $query->where('business_code', request()->business_code);
            }

            $locations = $query
                    ->latest()
                    ->paginate(10);

            return response()->json([
                'success'=>true,
                'message'=> 'Locations fetched successfully',
                'data'=> $locations,
            ],200);
        } catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=> 'Failed to fetch Locations',
                'error'=> $e->getMessage()
            ],401);
        }
    }

    public function store(CreateBusinessLocationRequest $request)
    {
        $data = $request->validated();
        try{
        $location = BusinessLocation::create([
            'business_code' => $request->business_code,
            'location_name' => $request->location_name,
            'address' => $request->address,
            'apartment' => $request->apartment,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Location created successfully',
            'data' => $location,
        ],200);

        }catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Location Creation Failed',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function show(BusinessLocation $businessLocation)
    {
        //$data = $request->validated(); ->change validation to this
        try{
                $businessLocation->load([
                    'business',
                    'locationServices',
                    'employeeShifts'
                ]);

                if (!$businessLocation) {
            return response()->json([
                'success' => false,
                'message' => 'Location not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Location retrieved successfully',
            'data' => $businessLocation,
        ], 200);

    } catch(\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'Location Not retrieved',
            'error' => $e->getMessage(),
            ],401);
}
    }

    public function update(UpdateBusinessLocationRequest $request, BusinessLocation $businessLocation)
    {
        //$data = $request->validated(); ->change validation to this
        try {
            $businessLocation->load([
                'business',
                'locationServices',
                'employeeShifts'
            ]);

            if (!$businessLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found',
                ], 404);
            }

            /*if($data->fails()){
                return response()->json([
                    'success' => false,
                    'message' => 'Validation Error.',
                    'error' => $data->errors(),
                ],401);
            }*/

            $businessLocation->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'data' => $businessLocation,
            ],200);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Location Not Updated',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function destroy(BusinessLocation $businessLocation)
    {
        //$data = $request->validated(); ->change validation to this
        try{
            $businessLocation->load([
                'business',
                'locationServices',
                'employeeShifts'
            ]);
            if (!$businessLocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Location not found',
                ],404);
            }

            $businessLocation->delete();
            return response()->json([
                'success' => true,
                'message' => 'Location deleted successfully',
                'data' => $businessLocation,
            ],200);

        } catch(\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Location Not Deleted',
                'error' => $e->getMessage(),
            ],401);
        }
    }
}
