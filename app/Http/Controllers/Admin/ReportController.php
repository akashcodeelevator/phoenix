<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
     /**
     * Display the filtered transaction report.
     */
    public function getReport(Request $request)
    {
        $query = Transaction::query();

        // Apply filters
        if ($request->filled('tx_type')) {
            $query->where('tx_type', $request->tx_type);
        }
        if ($request->filled('debit_credit')) {
            $query->where('debit_credit', $request->debit_credit);
        }
        if ($request->filled('wallet_type')) {
            $query->where('wallet_type', $request->wallet_type);
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        // Select fields and paginate
        //$transactions = $query->select(['amount', 'debit_credit', 'date', 'remark', 'status', 'u_code', 'wallet_type'])
        $transactions = $query->select([
            'transaction.amount',
            DB::raw("REPLACE(wallet_type, '_', ' ') as wallet_type"),  // Replacing underscores with spaces in wallet_type
            'debit_credit',
            DB::raw("DATE_FORMAT(date, '%d-%m-%y %H:%i:%s') as date"),
            'remark',
            DB::raw("CASE 
                        WHEN status = 0 THEN 'Pending'
                        WHEN status = 1 THEN 'Approve'
                        WHEN status = 2 THEN 'Cancel'
                        ELSE 'Unknown' 
                    END AS status"),  
            'u_code',
            DB::raw('users.username as username')  // Joining users table to get the username
        ])
        ->leftJoin('users', 'users.id', '=', 'transaction.u_code')
        ->paginate(10);
        //$sql = $query->toSql();
        //dd($sql);
        return response()->json($transactions, 200);
    }

    /**
     * Show the report page.
     */
    public function index()
    {
        return view('admin.reports.index');
    }
}
