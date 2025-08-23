<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Remove white spaces from inputs
            $request->merge([
                'user_name' => trim($request->user_name),
                'user_email' => trim($request->user_email),
            ]);

            // Validation
            $rules = [
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|string|email|max:255|unique:users',
                'user_password' => 'required|string|min:6',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'failure',
                    'message' => $validator->errors()
                ], 400);
            }
            $validatedData = $validator->validated();

            // Create new user in users table
            $user = User::create([
                'user_name' => $validatedData['user_name'],
                'user_email' => $validatedData['user_email'],
                'user_password' => Hash::make($validatedData['user_password']),
                'user_verifycode' => SendVerifyCodeController::generateCode($request),
            ]);

            // Set expiration date for the Verification code
            $user->expires_at = now()->addMinutes(2);
            $user->save();
            $response = [
                'status' => 'success',
                'user_data' => $user->only([
                    'user_id',
                    'user_name',
                    'user_email'
                ])
            ];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json("An unexpected error occurred. Please try again later.", 500);
        }
    }
}
