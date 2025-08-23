<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validate Inputs
            $validator = Validator::make($request->all(), [
                'user_email' => 'required|email|exists:users,user_email',
                'user_password' => 'required|string',
            ], [
                'user_email.exists' => 'We could not find an account with that email.',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422); // Return 422 Unprocessable Entity
            }

            // Find user email in users table
            $user = User::where('user_email', $request->user_email)->first();

            // If user account is not confirmed
            if ($user->user_approve != 1){
                return response()->json(['message' => 'Please verify your account before logging in.'], 400);
            }
            
            // If user email found and password is correct
            if ($user && Hash::check($request->user_password, $user->user_password)) {
                $token = $user->createToken('Personal Access Token')->plainTextToken;
                $response = [
                    'status' => 'success',
                    'user_data' => $user->only(['user_id', 'user_name', 'user_email', 'user_approve']),
                    'token' => $token
                ];
                return response()->json($response, 200);
            }
            return response()->json(['message' => 'Invalid credentials.'], 401);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failure',
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
}
