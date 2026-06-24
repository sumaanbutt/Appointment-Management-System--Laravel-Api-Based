<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiDataHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try{
            $query = User::query()
                ->with('business');

            if($request->business_code){
                $query->where(
                    'business_code',
                    request()->business_code
                );
            }

            if(request()->user_type){
                $query->where(
                    'user_type',
                    request()->user_type
                );
            }

            $users = ApiDataHelper::process( $query, $request );


            return response()->json([
                'success'=>true,
                'message'=> 'Users fetched successfully',
                'data'=> $users,
            ],200);
        }
        catch(\Exception $e){
            return response()->json([
                'success'=>false,
                'message'=> 'Failed to fetch Users',
                'error'=> $e->getMessage()
            ],401);
        }
    }

    public function store(CreateUserRequest $request)
    {
        $data = $request->validated();
        try {

            $user = User::create([
                'organization_code' => $request->organization_code,
                'business_code' => $request->business_code,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'user_type' => $request->user_type,
                'employee_type' => $request->employee_type,
                'status' => $request->status ?? 'ACTIVE',
            ]);

            if ($user->user_type === 'CLIENT') {

                Client::create([
                    'code' => 'CLT' . rand(100000, 999999),
                    'business_code' => $user->business_code,
                    'user_code' => $user->code,
                ]);

            }



            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load('roles', 'permissions')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User creation failed',
                'error' => $e->getMessage()
            ],401);
        }
    }

    public function show(User $user)
    {
        try{
            if(!$user){
                return response()->json([
                    'success' => false,
                    'message' => 'User not found',
                ],404);
            }

            return response()->json([
                'success' => true,
                'message' => 'User fetched successfully',
                'data' => $user,
            ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch User',
                'error' => $e->getMessage()
            ], 401);
        }
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        try {
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            $user->update($request->validated());

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password)
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ],201);

        } catch(\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User update failed',
                'error' => $e->getMessage()
            ],401);
        }
    }

    /*public function activate(User $user)
    {
        $user->update([
            'status' => 'ACTIVE'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User activated successfully'
        ]);
    }

    public function deactivate(User $user)
    {
        $user->update([
            'status' => 'INACTIVE'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User deactivated successfully'
        ]);
    }*/

    public function destroy(User $user)
    {
        try {
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'user not found',
                ], 404);
            }

            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ],201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'User deletion failed',
            'error' => $e->getMessage()
        ],401);
        }
    }
}
