<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailMailale;

class SigupController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validation
            $rules = [
                'user_name' => 'required|string|max:255',
                'user_email' => 'required|string|email|max:255|unique:users',
                'user_password' => 'required|string|min:6',
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json($validator->errors(), 400);
            }

            // Create new user in users table
            $user = User::create([
                'user_name' => $request->user_name,
                'user_email' => $request->user_email,
                'user_password' => Hash::make($request->user_password),
                'user_verifycode' => $this->generateCode($request),
            ]);
            $response = ['status' => 'success', 'user_data' => $user];

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            return response()->json("error $th", 400);
        }
    }

    public static function generateCode(Request $request)
    {
        // Create 5 Random numbers
        $verifyCode = random_int(10000, 99999);

        // Convert verify code to array of string then send it to user email
        $stringCode = str_split(strval($verifyCode));
        Mail::to($request->user_email)->send(new emailMailale($stringCode));
        return $verifyCode;
    }
}
