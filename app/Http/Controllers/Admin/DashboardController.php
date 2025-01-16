<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundRequest;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $newFundRequest = Transaction::where('tx_type','like','fund_request')->count();        
        $Totalusers = User::count(); 
        $Activeusers = User::where('active_status', 1)->count(); 
        $Totalinvestment = number_format(Order::sum('order_amount'), 2);
    
        // Pass each variable individually to the view
        return view('admin.dashboard', compact(
            'newFundRequest', 
            'Totalusers', 
            'Activeusers', 
            'Totalinvestment'
        ));
    }
}
