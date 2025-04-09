<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:20',
            'nickname' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|min:8',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'required|date|before:today',
            'image_name' => 'required|string',
            'image_file' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $imageData = base64_decode($request->image_file);
            $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9_.]/', '', $request->image_name);

            $path = public_path('uploads/images/');
            if (!file_exists($path)) {
                mkdir($path, 0755, true); 
            }

            file_put_contents($path . '/' . $filename, $imageData);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to save image',
            ], 500);
        }

        $email = $request->email ?? null;
        $phone = $request->phone ?? null;

        $user = User::create([
            'username' => $request->username,
            'nickname' => $request->nickname,
            'email' => $email,
            'password' => Hash::make($request->password),
            'phone' => $phone,
            'birthday' => $request->birthday,
            'avatar' => $filename,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user,
            'user_id' => $user->id,
        ], 201);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return response()->json([
            'status' => true,
            'message' => 'Logout successful'
        ], 200);
    }

    public function compareNickname(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nickname' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $nicknameExists = User::where('nickname', $request->nickname)->exists();

        if ($nicknameExists) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nickname is already taken',
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Nickname is available',
        ], 200);
    }

    public function loginAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_number' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email_number)
                    ->orWhere('phone', $request->email_number)
                    ->first();

        if ($user) {
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Login successful',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);
        } else {
            return response()->json([
                'status' => 'need_register',
                'message' => 'Not exist',
            ], 200);
        }
    }

    public function loginWithPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_number' => 'required|string', 
            'password' => 'required|string|min:8', 
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email_number)
                    ->orWhere('phone', $request->email_number)
                    ->first();

        if ($user) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => 'success',
                    'message' => 'Login successful',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'user' => $user
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid credentials'
                ], 200);
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 200);
        }
    }
}
