<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'User has been logged out successfully.',
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized: No active session found.',
        ], 401);
    }
}
