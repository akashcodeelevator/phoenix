<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class UserLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('user.auth.custom-login'); // User login view
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // Check if the user is already authenticated
        if (Auth::guard('web')->check()) {
            // Redirect to dashboard if already logged in
            return redirect()->route('user.dashboard');
        }
        if (Auth::guard('web')->attempt($credentials, $request->remember)) {

            return redirect()->route('user.dashboard');
        }
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout(Request $request)
    {
        // Logout the user guard
        Auth::guard('web')->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the session token
        $request->session()->regenerateToken();

        // Redirect to the user login page
        return redirect('/user/login')->with('success', 'You have been logged out.');
    }
}