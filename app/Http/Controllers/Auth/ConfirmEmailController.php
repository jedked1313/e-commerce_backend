<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Controllers\Controller;

class ConfirmEmailController extends Controller
{
    public function index(Request $request)
    {
        // If the user enter the correct sent code
        $user = User::where('user_email', $request->user_email)->first();
        $checkCode = $user->where('user_verifycode', $request->user_verifycode)->count();
        if ($checkCode > 0) {
            $user->update(['email_verified_at' => now(), 'user_approve' => 1]);
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['status' => 'success', 'message' => 'Your Email has been successfully confirmed.', 'user_data' => $user, 'token' => $token];
            return response()->json($response, 200);
        }
        // If the user enter wrong code
        $response = ['message' => 'Incorrect verify code'];
        return response()->json($response, 400);
    }
}
