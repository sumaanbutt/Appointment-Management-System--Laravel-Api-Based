<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Service\CreateServiceRequest;
use App\Http\Requests\Service\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        try{
            $query = Service::query();
            if(request()->business_code){
                $query->where(
                    'business_code',
                    request()->business_code
                );
            }
            $services = $query
                ->latest()
                ->paginate(10);
            return response()->json([
                'success'=>true,
                'data'=>$services
            ]);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Services',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function store(CreateServiceRequest $request)
    {
        $data = $request->validated();
        try{
            $service = Service::create([
                'business_code' => $request->business_code,
                'location_code' => $request->location_code,
                'service_name' => $request->service_name,
                'description' => $request->description ?? null,
                'time_duration' => $request->time_duration ?? null,
                'charges' => $request->charges ?? 0,
                'cost' => $request->cost ?? null,
                'currency' => $request->currency ?? 'PKR',
                'availability' => $request->availability,
                'duration_uom' => $request->duration_uom ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);

        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function show(Service $service)
    {
        try{
            $service->load([
                'business',
                'location',
            ]);

            if(!$service){
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ],401);
            }

            return response()->json([
                'success' => true,
                'message' => 'Service fetched successfully',
                'data' => $service,
            ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Service',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        $data = $request->validated();
        try{
            if(!$service){
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ],404);
            }

            $service->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Service updated successfully',
                'data' => $service
            ],200);

        } catch (\Exception $e){
        return response()->json([
            'success' => false,
            'message' => 'Failed to update service',
            'error' => $e->getMessage(),
        ],401);
        }
    }

    public function destroy(Service $service)
    {
        try {
            if (!$service) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service not found',
                ], 404);
            }

            $service->delete();

            return response()->json([
                'success' => true,
                'message' => 'Service deleted successfully'
            ], 200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Service deletion failed',
                'error' => $e->getMessage()
            ],401);
        }
    }
}
