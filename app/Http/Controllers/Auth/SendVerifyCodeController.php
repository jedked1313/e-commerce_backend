<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\emailMailale;
use App\Models\User;

class SendVerifyCodeController extends Controller
{
    public static function index(Request $request)
    {
        try {
            $user = User::where("user_email", $request->user_email)->firstOrFail();

            // Check if it's been more than 5 minutes since the last attempt and reset resend_attempts
            $timeDifference = $user->updated_at->diffInMinutes(now());
            if ($timeDifference > 5) {
                $user->resend_attempts = 0;  // Reset resend_attempts after 5 minutes
            }

            // Check if a verification code has been sent recently
            $timeDifference = $user->updated_at->diffInMinutes(now());
            if ($timeDifference < 1 || $user->resend_attempts > 2) { // Prevent resending within 5 minutes
                return response()->json([
                    'status' => 'failure',
                    'message' => 'You can only resend the code every 5 minutes.',
                ], 429);
            }

            $user->user_verifyCode = self::generateCode($request);
            $user->resend_attempts = $user->resend_attempts + 1;
            $user->expires_at = now()->addMinutes(2);
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Verification code has been sent successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'failure',
                'message' => 'Could not send verification email. Please try again.'
            ], 500);
        }
    }

    public static function generateCode(Request $request)
    {
        try {
            // Create 5 Random numbers
            $verifyCode = random_int(10000, 99999);

            // Convert verify code to array of string to make it iterable
            $stringCode = str_split($verifyCode);
            // Send code to user email
            // Mail::to($request->user_email)->send(new emailMailale($stringCode));
            return $verifyCode;
        } catch (\Throwable $th) {
            return response()->json('Could not send verification email. Please try again.', 500);
        }
    }
}
