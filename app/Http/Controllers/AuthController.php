<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use App\Mail\SendPasswordMail;
use App\Mail\SendOtpMail;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate login data
        $validatedData = $request->validate([
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);
    
        // Check user credentials
        $credentials = [
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'token' => $token,
                'message' => 'Login successful',
            ], 200);
        }
    
        return response()->json([
            'message' => 'Invalid credentials',
        ], 401);
    }
    
    public function resetPassword(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'current_password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6|confirmed', // 'confirmed' ensures it matches the 'new_password_confirmation'
        ]);
    
        $user = Auth::user();
    
        // Check if the current password matches the stored password
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 400);
        }
    
        // Update the password
        $user->password = Hash::make($validatedData['new_password']);
        $user->save();
    
        return response()->json([
            'message' => 'Password successfully changed.',
        ], 200);
    }
     
    // login
    public function sendOtp(Request $request)
    {
    
        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        Cache::put('otp_' . $request->email, $otp, now()->addSeconds(30));

        Mail::to($request->email)->send(new SendOtpMail($otp));

        return response()->json(['success' => true, 'message' => 'OTP sent']);
    }

    public function verifyOtp(Request $request)
    {
        // Validate email and OTP fields
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        // Retrieve the cached OTP
        $cachedOtp = Cache::get('otp_' . $request->email);

        // Verify the OTP
        if ($cachedOtp && $cachedOtp == $request->otp) {
            return response()->json(['success' => true, 'message' => 'OTP verified']);
        }

        // Return an error if the OTP is invalid
        return response()->json(['success' => false, 'message' => 'Invalid or expired OTP'], 400);
    }

    public function sendPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $password = Str::random(8) . Str::upper(Str::random(2)) . Str::random(2);

        $user = User::updateOrCreate(
            ['email' => $request->email],
            ['password' => bcrypt($password)]
        );

        Mail::to($request->email)->send(new SendPasswordMail($password));

        return response()->json(['success' => true, 'message' => 'Password sent']);
    }
}
