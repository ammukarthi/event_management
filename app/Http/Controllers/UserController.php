<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,organizer,customer',
            'phone' => 'required|regex:/^[6-9][0-9]{9}$/',
        ]);

        if($validate->fails()){

            return response()->json(['status' => 'error','message' => $validate->errors()->first()],400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role,
        ]);

        return response()->json([
            'status' => 'success',
            'message'=>'User registered successfully'
        ],201);
    }

    public function login(Request $request){

        $validate = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if($validate->fails()){

            return response()->json(['status' => 'error','message' => $validate->errors()->first()],400);
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user
        ],200);

    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ],200);
    }

    public function profile(Request $request){
        
        $user = $request->user();

        return response()->json([
            'status' => 'success',
            'user' => $user
        ],200);
    }
}
