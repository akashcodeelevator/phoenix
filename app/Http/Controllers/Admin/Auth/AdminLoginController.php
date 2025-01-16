<?php
namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.custom-login'); // Admin login view
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        
         // Check if the user is already authenticated
         if (Auth::guard('admin')->check()) {
            // Redirect to dashboard if already logged in
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('admin')->attempt($credentials, $request->remember)) {
            
            return redirect()->route('admin.dashboard');
            
        }
        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    // public function logout()
    // {
    //     Auth::guard('admin')->logout();
    //     //return redirect('/admin/login');
    //     return redirect()->route('admin.login');
    // }
    public function logout(Request $request)
    {
        // Logout the admin guard
        Auth::guard('admin')->logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the session token
        $request->session()->regenerateToken();

        // Redirect to the admin login page
        return redirect('/admin/login')->with('success', 'You have been logged out.');
    }
}
