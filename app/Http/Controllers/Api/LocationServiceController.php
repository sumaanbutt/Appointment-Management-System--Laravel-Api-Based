<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LocationServices\CreateLocationServiceRequest;
use App\Http\Requests\LocationServices\UpdateLocationServiceRequest;
use App\Models\LocationServices;

class LocationServiceController extends Controller
{
    /**
     * Display all location services
     */
    public function index()
    {
        try{
        $locationServices = LocationServices::with([
            'business',
            'location',
            'service',
        ])->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Location services fetched successfully',
            'data' => $locationServices,
        ]);
        } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch location services',
            'error' => $e->getMessage(),
        ]);
        }
    }

    /**
     * Store new location specific service
     */
    public function store(CreateLocationServiceRequest $request)
    {
        try {
            $locationService = LocationServices::create(
                $request->validated()
            );

            return response()->json([
                'success' => true,
                'message' => 'Location service created successfully',
                'data' => $locationService,
            ], 201);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to create location service',
                'error' => $e->getMessage(),
            ],401);
        }
    }
    /**
     * Show single location service
     */
    public function show(LocationServices $locationService)
    {
        try{
        return response()->json([
            'success' => true,
            'message' => 'Location service fetched successfully',
            'data' => $locationService->load([
                'business',
                'location',
                'service',
            ]),
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch location service',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    /**
     * Update location service
     */
    public function update(UpdateLocationServiceRequest $request, LocationServices $locationService) {
        try{
        $locationService->update(
            $request->validated()
        );

        return response()->json([
            'success' => true,
            'message' => 'Location service updated successfully',
            'data' => $locationService,
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Failed to update location service',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    /**
     * Delete location service
     */
    public function destroy(LocationServices $locationService)
    {
        try{
        $locationService->delete();

        return response()->json([
            'success' => true,
            'message' => 'Location service deleted successfully',
        ]);
    }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete location service',
                'error' => $e->getMessage(),
            ],401);
        }
    }
}
