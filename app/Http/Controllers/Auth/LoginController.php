<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function index(Request $request){
        // Validate Inputs
        $rules = [
            'user_email' => 'required',
            'user_password' => 'required|string',
        ];
        $validator = Validator::make($request->all(),$rules);
        if($validator->fails()){
            return response()->json($validator->errors(),400);
        }
        // Find user email in users table
        $user = User::where('user_email',$request->user_email)->first();
        // If user email found and password is correct
        if($user && Hash::check($request->user_password, $user->user_password)){
            $token = $user->createToken('Personal Access Token')->plainTextToken;
            $response = ['status' => 'success','user_data' => $user,'token' => $token];
            return response()->json($response, 200);
        }
        $response = ['message' => 'Incorrect email or password'];
        return response()->json($response, 400);
    }
}
