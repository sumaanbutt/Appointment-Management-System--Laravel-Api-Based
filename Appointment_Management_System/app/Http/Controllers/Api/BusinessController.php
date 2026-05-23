<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Business\CreateBusinessRequest;
use App\Http\Requests\Business\UpdateBusinessRequest;
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    public function index()
    {
        try{
            $businesses = Business::with('organization', 'owner', 'locations')->latest()->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Businesses fetched successfully',
            'data' => $businesses,
        ],200);

        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch businesses',
                'error' => $e->getMessage()
            ],401);
        }
    }

    public function store(CreateBusinessRequest $request)
    {
        try {
                $business = Business::create([
                    'organization_code' => $request->organization_code,
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'description' => $request->description,
                    'timezone'=> $request->timezone,
                    'status' => $request->status,
            ]);


            User::create([
                'code' => 'USR'.rand(100000,999999),
                'organization_code' => $business->organization_code,
                'business_code' => $business->code,
                'name' => $business->name.' Owner',
                'email' =>
                    strtolower(str_replace(
                            ' ',
                            '',
                            $business->name
                        )
                    ).'@owner.com',

                'password' => Hash::make('password123'),
                'user_type' => 'BUSINESS_OWNER',
                'status' => 'ACTIVE',
            ]);


            return response()->json([
                'success'=>true,
                'message'=> 'Business + Owner created successfully',
                'data'=>$business
            ],201);
        }

        catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=>'Business creation failed',
                'error'=>$e->getMessage()
            ],401);
        }
    }

    public function show(Business $business)
    {
        try{
        $business->load([
            'organization',
            'owner',
            'locations',
            'services',
            'users',
            'appointments',
        ])->find($business);

        if(!$business){
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
            ],401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Business fetched successfully',
            'data' => $business,
        ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch business',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateBusinessRequest $request, Business $business)
    {
        $data = $request->validated();

        try {
            /*$business = Business::find($business);

            if(!$business){
            return response()->json([
                'success' => false,
                'message' => 'Business not found',
            ],404);
            }

            if($data->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation errors',
                    'errors' => $data->errors()
                ],422);
            }*/

            $business->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Business updated successfully',
                'data' => $business
            ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Business update failed',
                'error' => $e->getMessage()
            ],401);
        }
    }

    public function destroy(Business $business)
    {
        try {
            /*$business = Business::find($business);
            if (!$business) {
                return response()->json([
                    'success' => false,
                    'message' => 'Business not found',
                ], 404);
            }*/

            $business->delete();

            return response()->json([
                'success' => true,
                'message' => 'Business deleted successfully'
            ], 200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Business deletion failed',
                'error' => $e->getMessage()
            ],401);
        }
    }
}


/*
AuthController
OrganizationController
BusinessController
UserController
ClientController
LocationController
ServiceController
UserShiftScheduleController
AppointmentController
AppointmentParticipantController
AppointmentHistoryController
AppointmentServiceController
LocationServiceController
AppointmentRecurrenceController
ChargeController
AppointmentChargeController
InvoiceController
UserAbilityController
DashboardController
ProfileController
 */
