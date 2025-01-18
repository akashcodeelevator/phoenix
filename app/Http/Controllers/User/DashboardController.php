<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PinHistory;
use App\Models\Transaction;
use App\Models\WalletType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $newFundRequest = Transaction::where('tx_type', 'like', 'fund_request')->count();
        $Totalinvestment = number_format(Order::sum('order_amount'), 2);
        $user = Auth::guard('web')->user();
        $package = PinHistory::where('user_id', $user->id)->first();
        $growth_rate = 60; // Example value  
        $colors = ['chart2', 'chart3', 'chart7', 'chart8', 'chart17', 'chart19'];
        $colorIndex = 0;
        $incomeData  =  WalletType::where('wallet_type', 'income')
            ->where('status', 1)
            ->where('universal', 1)
            ->get();
        // Pass each variable individually to the view
        return view('user.dashboard', compact(
            'newFundRequest',
            'Totalinvestment',
            'user',
            'package',
            'growth_rate',
            'incomeData',
            'colors',
        ));
    }
    public function myprofile()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Pass user data to the view
        return view('user.myprofile', compact('user'));
    }
    public function edit()
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Pass user data to the view
        return view('user.editprofile', compact('user'));
    }

    /**
     * Update the user's profile details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'address' => 'nullable|string|max:500',
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Update the user's details
        $user->update([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
        ]);

        // Redirect back with a success message
        return redirect()->route('user.editprofile')->with('success', 'Profile updated successfully.');
    }
}