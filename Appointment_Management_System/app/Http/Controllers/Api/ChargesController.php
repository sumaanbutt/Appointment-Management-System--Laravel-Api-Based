<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Charge\CreateChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Models\Charge;
use Illuminate\Http\Request;

class ChargesController extends Controller
{

    public function index(Request $request)
    {
        try{
            $query = Charge::query()
                ->with('business');

            if(request()->business_code){
                $query->where(
                    'business_code',
                    request()->business_code
                );
            }

            $charge = ApiDataHelper::process( $query, $request );

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
        try{
            $data = $request->validated();


            // Default status if not provided in request logic
            $data['status'] = $data['status'] ?? 'ACTIVE';
            $data['auto_apply'] = $request->input('auto_apply', false);

            $charge = Charge::create($data);

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
//        $data = $request->validated();
        try{

//            dd([
//                'request_auto_apply' => $request->auto_apply,
//                'type' => gettype($request->auto_apply),
//                'all' => $request->all(),
//            ]);
            if(!$charge){
                return response()->json([
                    'status' => false,
                    'message' => 'charge not found',
                    'data' => $charge
                ],404);
            }

            $data = $request->validated();
            $charge->update($data);

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
