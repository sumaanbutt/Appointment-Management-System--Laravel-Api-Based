<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CreateClientRequest;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index()
    {
    try{
        $clients = Client::with('user')->latest()->paginate(10);

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

    public function store(CreateClientRequest $request, Client $client)
    {
        $data = $request->validated();

        try{
            $user = User::where(
                'code',
                $request->user_code
            )->firstOrFail();

            $user->update([
                'user_type' => 'CLIENT'
            ]);

            $client = Client::create([
                'business_code' => $request->business_code,
                'user_code' => $request->user_code,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Client created successfully',
                'data' => $client,
            ],200);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Client',
                'error' => $e->getMessage(),
            ],401);
        }
    }

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
