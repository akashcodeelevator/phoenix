<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        // Eager load the topupFromUser relationship to get the user's name
        $orders = Order::with('topupFromUser') // Load related user data
            ->leftJoin('users', 'users.id', '=', 'orders.u_code')
            ->select('orders.id', 'users.username', 'order_amount', 'orders.created_at', 'orders.tx_user_id') // Select specific columns
            ->paginate(10); // Paginate with 10 items per page

        return view('admin.orders.index', compact('orders'));
    }
}
