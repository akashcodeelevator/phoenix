<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedInfo;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletType;
use Illuminate\Http\Request;

class WithdrawRequestController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function index($status = null) {
        // Get the total number of withdraw requests
        $transactions = Transaction::where( 'tx_type', 'like', 'withdrawal' )
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
        return view( 'admin.withdrawrequest', compact( 'transaction' ) );
    }
    public function withdrawrequestsshow($id){
        $transactions = Transaction::find($id);
        $user =  User::find($transactions->u_code);  
        return view('admin.withdraw.show', compact('transactions','user'));
    }
    public function withdraw_request_approve(Request $request){        
        $transaction = Transaction::find($request->withdraw_request_id);        
        $transaction->reason= $request->reason;
        $transaction->remark= $request->reason;
        if(@$request->approve_btn=='approve'){            
           $transaction->status=1;
        }
        if(@$request->cancel_btn=='cancel'){
            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
            $source =@$transaction->wallet_type??'main_wallet';
            $source_1 = WalletType::where('slug', 'like', $source)
                            ->where('wallet_type', 'wallet')
                            ->where($reg_type, 1)
                            ->value('wallet_column');
            $amount =$transaction->amount;
            $userWallet = UserWallet::firstOrNew(['u_code' => $transaction->u_code]); // Use firstOrNew to avoid duplicate queries
            $source_1_amount = $userWallet->$source_1 ?? 0;
            $userWallet->$source_1 = $source_1_amount + $amount;
            // Update wallet balance
            $userWallet->save();
            $transaction->status=2;
        }
        $transaction->save();
        return redirect()->route('admin.withdrawrequest.index', ['status' => 'pending']);
    }
}
