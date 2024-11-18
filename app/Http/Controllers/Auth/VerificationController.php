<?php
// backend-laravel-server/app/Http/Controllers/Auth/VerificationController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Auth\Events\Verified;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('sendVerificationEmail', 'verifyEmail');
    }

    public function sendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        // Generate a new 6-digit verification code
        $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $request->user()->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => Carbon::now()->addMinutes(60),
        ]);

        // Send email verification code
        Mail::to($request->user()->email)->send(new VerificationCodeMail($request->user()));

        return response()->json(['message' => 'Verification code sent!'], 200);
    }

    public function verifyEmail(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'code' => 'required|string',
        ]);

        $user = User::findOrFail($request->id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 200);
        }

        if ($user->verification_code === $request->code && Carbon::now()->lessThanOrEqualTo($user->verification_code_expires_at)) {
            $user->markEmailAsVerified();
            $user->update(['email_verified' => true]); // Update email_verified field
            event(new Verified($user));
            return response()->json(['message' => 'Email verified successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid or expired verification code.'], 400);
    }
}