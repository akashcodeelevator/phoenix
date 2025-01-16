<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedInfo;
use App\Models\Transaction;
use App\Models\UserWallet;
use App\Models\WalletType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;

class FundRequestController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index($status = null) {
        // Get the total number of fund requests
        $transactions = Transaction::where( 'tx_type', 'like', 'fund_request' )
                        ->join( 'users', 'transaction.u_code', '=', 'users.id' ) // Join with users table
                        ->select( 'transaction.*', 'users.username','users.name' );
         if ($status) {
            if ($status == 'approve') {
                $transactions->where('transaction.status', 1); // Approved
            } elseif ($status == 'pending') {
                $transactions->where('transaction.status', 0); // Pending
            } elseif ($status == 'cancel') {
                $transactions->where('transaction.status', 2); // Cancelled
            }
        }
        $transaction = $transactions->get();
        // Pass the transaction data to the view
        return view( 'admin.fundrequest', compact( 'transaction' ) );
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create() {
        return view('admin.fund.create');
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store( Request $request ) {
        //$min_transfer_limit = Setting::->where('setting_name', 'min_transfer_limit')->value('setting_value');
        $validated = $request->validate([
            'username' => 'required|string|exists:users,username',
            'amount' => 'required|numeric',
            'remark' => 'required|string',
            'wallet_type' => 'required|in:main_wallet,fund_wallet',
            'transaction_type' => 'required|in:debit,credit',
        ]);
        // Get input values
        $amount = abs($validated['amount']);

        // Get user ID by username
        $user = User::where('username', $validated['username'])->first();
        $u_id = $user->id;
        // Get current wallet balance
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source =$validated['wallet_type'];
        $source_1 = WalletType::where('slug', 'like', $source)
                        ->where('wallet_type', 'wallet')
                        ->where($reg_type, 1)
                        ->value('wallet_column');
    
       $userWallet = UserWallet::firstOrNew(['u_code' => $u_id]); // Use firstOrNew to avoid duplicate queries
       $open_wallet = $source_1_amount = $userWallet->$source_1 ?? 0;
       //$closing_wallet = $userWallet->$source_1 = $source_1_amount + $amount;
        // Handle credit and debit operations
        if ($validated['transaction_type'] == 'credit') {
            // If it's a credit, add the amount to the wallet
            $closing_wallet = $open_wallet + $amount;
            // Set the wallet value
            $userWallet->$source_1 = $closing_wallet;
            $remark = "$user->username received $amount from admin";
        } else {
            // If it's a debit, subtract the amount from the wallet
            if ($open_wallet < $amount) {
                // Prevent overdrawing (ensure balance is sufficient)
                return redirect()->back()->withErrors(['amount' => 'Insufficient balance'])->withInput();
            }
            $closing_wallet = $open_wallet - $amount;
            // Set the wallet value
            $userWallet->$source_1 = $closing_wallet;
            $remark = "$user->username sent $amount to admin";
        }
        
       // Update wallet balance
       $userWallet->save();

        // Create transaction record
        $transaction = [
            'wallet_type' => $validated['wallet_type'],
            'tx_type' => 'admin_'.$validated['transaction_type'],
            'debit_credit' => $validated['transaction_type'],
            'u_code' => $u_id,
            'amount' => $amount,
            'reason' => $validated['remark'],
            'date' => Carbon::now(),
            'status' => 1, // Assuming 1 is for success
            'open_wallet' => $open_wallet,
            'closing_wallet' => $closing_wallet,
            'remark' => $remark,
        ];

        // Insert transaction data
        $inserted = Transaction::create($transaction);

        // Redirect back to the form or dashboard
        return redirect()->route('admin.fundrequests.history')
                         ->with('success', 'Fund Add successfully.');
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show( $id ) {
        //
    }
    public function fundrequestsshow($id){
        $transactions = Transaction::find($id);
        $user =  User::find($transactions->u_code);  
        return view('admin.fund.show', compact('transactions','user'));
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit( $id ) {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function update( Request $request, $id ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function destroy( $id ) {
        //
    }
    public function history($status = null){
        $transactions = Transaction::whereIn( 'tx_type', ['admin_credit','admin_debit'])
                ->join( 'users', 'transaction.u_code', '=', 'users.id' )
                ->join('wallet_types', 'transaction.wallet_type', '=', 'wallet_types.slug')
                ->select( 'transaction.*', 'users.username','users.name','wallet_types.name as wallet_type_name' );
        if ($status) {
        if ($status == 'approve') {
        $transactions->where('transaction.status', 1); // Approved
        } elseif ($status == 'pending') {
        $transactions->where('transaction.status', 0); // Pending
        } elseif ($status == 'cancel') {
        $transactions->where('transaction.status', 2); // Cancelled
        }
        }
        $transaction = $transactions->get();
        // Pass the transaction data to the view
        return view( 'admin.fund.fundhistory', compact( 'transaction' ) );
    }
    public function fund_request_approve(Request $request){

        
        $transaction = Transaction::find($request->fund_request_id);        
        $transaction->reason= $request->reason;
        if(@$request->approve_btn=='approve'){
            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
            $source =@$transaction->wallet_type??'fund_wallet';
            $source_1 = WalletType::where('slug', 'like', $source)
                            ->where('wallet_type', 'wallet')
                            ->where($reg_type, 1)
                            ->value('wallet_column');
            $amount =$transaction->amount;
           $userWallet = UserWallet::firstOrNew(['u_code' => $transaction->u_code]); // Use firstOrNew to avoid duplicate queries
           $open_wallet = $source_1_amount = $userWallet->$source_1 ?? 0;
           $closing_wallet = $userWallet->$source_1 = $source_1_amount + $amount;
           // Update wallet balance
           $userWallet->save();
           $transaction->status=1;
        }
        if(@$request->cancel_btn=='cancel'){
            $transaction->status=2;
        }
        $transaction->save();
        return redirect()->route('admin.fundrequests.index', ['status' => 'pending']);
    }
}
