<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function showRegister()
    {
        return view('register');
    }

    public function showLogin()
    {
        return view('login');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => '+234' . ltrim($request->phone, '0'), // Format Nigerian number
        ]);

        return response()->json(['success' => true]);
    }

    public function sendCode(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);
        $email = $request->email;
        $code = rand(100000, 999999);

        $user = User::where('email',  $email)->first();
        $user->login_code = $code;
        $user->code_expires_at = now()->addMinutes(10);
        $user->save();

        // Send email (or you can use a queue later)
        // Mail::raw("Your login code is: {$code}", function ($message) use ($email) {
        //     $message->to($email)->subject('Your Login Code');
        // });

        return response()->json(['success' => true]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)
            ->where('login_code', $request->code)
            ->where('code_expires_at', '>=', now())
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Invalid or expired code.']);
        }

        $user->login_code = null;
        $user->code_expires_at = null;
        $user->save();

        auth()->login($user);
        return response()->json(['success' => true]);
    }

    
}
