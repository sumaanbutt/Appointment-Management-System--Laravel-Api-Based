<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
    try{
        $query = Client::query()
            ->with('user');

        if ($request->location_code) {
            $query->where(
                'location_code',
                $request->location_code
            );
        }

        $clients = ApiDataHelper::process( $query, $request );

        return response()->json([
            'success' => true,
            'message' => 'Clients fetched successfully',
            'data' => $clients,
        ],200);

    } catch(\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch Clients',
            'error' => $e->getMessage(),
        ],401);
    }
    }

    public function store(CreateClientRequest $request)
    {
        // All validated fields are handled inside the FormRequest
        $data = $request->validated();

        try {
            return DB::transaction(function () use ($request) {
                // 1. Create the system user record first
                $user = User::create([
                    'code' => 'USR' . rand(100000, 999999),
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone' => $request->phone,
                    'password' => Hash::make($request->password),
                    'user_type' => 'CLIENT',
                    'status' => strtoupper($request->status ?? 'ACTIVE'),
                ]);

                // 2. Map user code onto profile schema creation
                $client = Client::create([
                    'business_code' => $request->business_code,
                    'user_code' => $user->code,
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Client and User account created successfully',
                    'data' => $client->load('user'),
                ], 201);
            });

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Client',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    // ... rest of the resource methods stay the same


    public function show(Client $client)
    {
        try{
            if(!$client){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ],404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Client fetched successfully',
                'data' => $client,
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch Client',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $data = $request->validated();

        try{
           if(!$client){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ],404);
            }

            $client->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Client Updated successfully',
                'data' => $client,
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Client',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function destroy(Client $client)
    {
        try{
            if(!$client){
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found',
                ],404);
            }

            $client->delete();

            return response()->json([
                'success' => true,
                'message' => 'Client Deleted successfully',
                'data' => $client,
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete Client',
                'error' => $e->getMessage(),
            ],401);
        }
    }
}
