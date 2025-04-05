<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            // Generate Sanctum token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ], 200);
    }
}
