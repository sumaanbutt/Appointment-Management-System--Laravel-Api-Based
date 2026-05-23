<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAbility\CreateUserAbilityRequest;
use App\Http\Requests\UserAbility\UpdateUserAbilityRequest;
use App\Models\UserAbility;
use Illuminate\Http\Request;

class UserAbilityController extends Controller
{
    public function index()
    {
        try{
            $abilities = UserAbility::latest()->paginate(10);

            return response()->json([
                'success' => true,
                'message' => 'User Abilities fetched successfully',
                'data' => $abilities,
            ],200);

        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Abilities',
                'error' => $e->getMessage(),
            ],401);
        }
    }

    public function store(CreateUserAbilityRequest $request)
    {
        $data = $request->validated();
        try {

            $ability = UserAbility::create([
                    'business_code' => $request->business_code,
                    'user_code' => $request->user_code,
                    'ability' => $request->ability,
                    'status' => $request->status ?? 'ACTIVE',
                    'added_by_code' => auth()->user()->code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User Ability created successfully',
                'data' => $ability,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Ability creation failed',
                'error' => $e->getMessage()
            ],401);
        }
    }

    public function show(UserAbility $userAbility)
    {
        try{
            if(!$userAbility){
                return response()->json([
                    'success' => false,
                    'message' => 'User Ability not found',
                ],404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User Ability fetched successfully',
                'data' => $userAbility,
            ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User Ability',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateUserAbilityRequest $request, UserAbility $userAbility)
    {
        $data = $request->validated();

        try {
            if (!$userAbility) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Ability not found',
                ], 404);
            }

            $userAbility->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'User Ability updated successfully',
                'data' => $userAbility,
            ],201);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Ability update failed',
                'error' => $e->getMessage()
            ],401);
        }
    }

    public function destroy(UserAbility $userAbility)
    {
        try {
            if (!$userAbility) {
                return response()->json([
                    'success' => false,
                    'message' => 'User Ability not found',
                ], 404);
            }

            $userAbility->delete();
            return response()->json([
                'success' => true,
                'message' => 'User Ability deleted successfully',
            ],201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User Ability deletion failed',
                'error' => $e->getMessage()
            ],401);
        }
    }
}
