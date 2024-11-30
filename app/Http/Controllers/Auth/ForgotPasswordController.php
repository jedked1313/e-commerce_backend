<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Auth\SigupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    public function forgotPassword(Request $request)
    {
        // Check if enterd email exists
        $rules = [
            'user_email' => 'required|string|email|max:255',
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        // Find user email in users table
        $user = User::where('user_email', $request->user_email)->first();
        // If user email found
        if ($user) {
            // Update verify code then send it to email
            $user->user_verifycode = SigupController::generateCode($request);
            $user->save();
            $response = ['status' => 'success', 'email' => $request->user_email, 'verify_code' => $user->user_verifycode];
            return response()->json($response, 200);
        }
        $response = ['message' => 'This email does not exist'];
        return response()->json($response, 400);
    }

    public function verifyCode(Request $request)
    {
        // If the user enter the correct sent code
        $checkCode = User::where('user_email', $request->user_email)->where('user_verifycode', $request->user_verifycode)->count();
        if ($checkCode > 0) {
            $response = ['status' => 'success', 'message' => 'Correct Verify Code.'];
            return response()->json($response, 200);
        }
        // If the user enter wrong code
        $response = ['message' => 'Incorrect Verify Code'];
        return response()->json($response, 400);
    }

    public function resetPassword(Request $request)
    {
        $user = User::where('user_email', $request->user_email)->first();
        $rules = [
            'user_password' => 'required|string|min:6',
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        try {
            $user->user_password = Hash::make($request->user_password);
            $user->save();
            $response = ['status' => 'success', 'message' => 'Your password has been successfully restored.'];
            return response()->json($response, 200);
        } catch (\Throwable $th) {
            $response = ['status' => 'failure','message' => 'Failure !'];
            return response()->json($response, 400);
        }
    }
}
