<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class ConfirmEmailController extends Controller
{
    public function index(Request $request)
    {
        // Validate inputs
        $validated = $request->validate([
            'user_email' => 'required|email',
            'user_verifycode' => 'required',
        ]);

        // Attempt to retrieve the user by email
        $user = User::where('user_email', $validated['user_email'])->firstOrFail();

        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Check if the code has expired
        if (now()->greaterThan($user->expires_at)) {
            return response()->json('The verification code has expired. Please request a new one.', 400);
        }

        // Check if verification code is correct
        if ($user->user_verifycode != $validated['user_verifycode']) {
            return response()->json(['message' => 'Incorrect verification code'], 400);
        }

        // Proceed with email verification
        try {
            $user->email_verified_at = now();
            $user->user_approve = 1;
            $user->save();

            // Create a new token
            $token = $user->createToken('Personal Access Token')->plainTextToken;

            // Send success response
            return response()->json([
                'status' => 'success',
                'user_data' => $user->only(['user_id', 'user_name', 'user_email', 'user_email', 'user_approve']),
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
