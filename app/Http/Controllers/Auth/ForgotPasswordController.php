<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\SignupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        // Check if enterd email exists
        $rules = [
            'user_email' => 'required|email|max:255|exists:users,user_email',
        ];
        $messages = [
            'user_email.exists' => 'The provided email address does not exist in our records.',
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        // Find user email in users table
        $user = User::where('user_email', $request->user_email)->first();
        // If user email found
        if ($user) {
            try {
                // Update verify code then send it to email
                $user->user_verifycode = SignupController::generateCode($request);
                $user->save();
                $response = ['status' => 'success', 'message' => 'A verification code has been sent to ' . $request->user_email . '.'];
                return response()->json($response, 200);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'There was an error processing your request.'
                ], 500);
            }
        }
        $response = ['message' => 'This email does not exist'];
        return response()->json($response, 400);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email|exists:users,user_email',
            'user_verifycode' => 'required|numeric',
        ]);

        // If validation fails, return error response
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid input data.',
                'errors' => $validator->errors()
            ], 422);
        }
        // If the user enter the correct sent code
        $checkCode = User::where('user_email', $request->user_email)
            ->where('user_verifycode', $request->user_verifycode)
            ->exists();
        if ($checkCode > 0) {
            $response = ['status' => 'success', 'message' => 'Correct Verify Code.'];
            return response()->json($response, 200);
        }
        // If the user enter wrong code
        $response = [
            'status' => 'failure',
            'message' => 'Incorrect verification code.'
        ];
        return response()->json($response, 401);
    }

    public function resetPassword(Request $request)
    {
        // Validate input fields first
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email|exists:users,user_email',
            'user_password' => 'required|string|min:6',
        ]);

        // If validation fails, return the validation errors
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Retrieve the user by email
        $user = User::where('user_email', $request->user_email)->first();

        // If user not found
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        // Update the user's password
        try {
            $user->user_password = Hash::make($request->user_password);
            $user->save();
            $response = ['status' => 'success', 'message' => 'Your password has been successfully reseted.'];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
