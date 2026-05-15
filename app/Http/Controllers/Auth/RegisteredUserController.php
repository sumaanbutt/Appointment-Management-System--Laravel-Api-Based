<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Organization;
use App\Models\Business;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        try{
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_code' => ['required','exists:businesses,code'
    ],
        ]);
    } catch (ValidationException $e) {

return response()->json([
'success' => false,
'message' => 'Validation failed',
'errors' => $e->errors(),
], 422);
}

        $organization = Organization::first();


        $user = User::create([
            'organization_code' => $organization->code,
            'business_code' => $request->business_code,

            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),

            'user_type' => 'CLIENT',
            'status' => 'ACTIVE',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }
}
