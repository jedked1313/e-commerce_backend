<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        try {
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
                // Update verify code then send it to email
                SendVerifyCodeController::index($request);
                $response = ['status' => 'success', 'message' => 'A verification code has been sent to ' . $request->user_email . '.'];
                return response()->json($response, 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'failure',
                'message' => 'User not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'There was an error processing your request.'
            ], 500);
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
                'status' => 'failure',
                'message' => 'Invalid input data.',
                'errors' => $validator->errors()
            ], 400);
        }

        // Fetch the user by email and verify the code
        $user = User::where('user_email', $request->user_email)
            ->where('user_verifycode', $request->user_verifycode)
            ->first();

        // Check if user is found and verify the code
        if (!$user) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Incorrect verification code.'
            ], 422);
        }

        if (now()->greaterThan($user->expires_at)) {
            return response()->json([
                'status' => 'failure',
                'message' => 'The verification code has expired. Please request a new one.'
            ], 200);
        }

        // If the code is valid and not expired
        $response = [
            'status' => 'success',
            'message' => 'Correct verification code.'
        ];

        return response()->json($response, 200);
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
