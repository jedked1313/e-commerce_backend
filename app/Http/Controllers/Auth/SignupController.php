<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailMailale;

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
                    'errors' => $validator->errors()
                ], 400);
            }

            // Create new user in users table
            $validatedData = $validator->validated();
            $user = User::create([
                'user_name' => $validatedData['user_name'],
                'user_email' => $validatedData['user_email'],
                'user_password' => Hash::make($validatedData['user_password']),
                'user_verifycode' => $this->generateCode($request),
            ]);
            $response = ['status' => 'success', 'user_data' => $user->only(['user_id', 'user_name', 'user_email'])];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json("An unexpected error occurred. Please try again later.", 500);
        }
    }

    public static function generateCode(Request $request)
    {
        try {
            // Create 5 Random numbers
            $verifyCode = random_int(10000, 99999);

            // Convert verify code to array of string to make it iterable then send it to user email
            $stringCode = str_split($verifyCode);
            Mail::to($request->user_email)->send(new emailMailale($stringCode));
            return $verifyCode;
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Could not send verification email. Please try again.'], 500);
        }
    }
}
