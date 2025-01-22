<?php

namespace App\Http\Controllers\User;

use App\Models\AdvancedInfo;
use App\Models\Order;
use App\Models\PinDetail;
use App\Models\UserWallet;
use App\Models\WalletType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class FundController extends Controller
{
    /**
     * Show the fund transfer form.
     */
    public function fundTransferform()
    {
        $user = Auth::user();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = 'main_wallet';
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
        $main_wallet  = $userWallet->$source_1 ?? 0;
        $source2 = 'fund_wallet';
        $source_2 = WalletType::where('slug', 'like', $source2)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $fund_wallet  = $userWallet->$source_2 ?? 0;
        return view('user.addfund', compact('user', 'main_wallet', 'fund_wallet')); // Ensure the Blade file exists at resources/views/user/addfund.blade.php
    }

    /**
     * Handle fund transfer submission.
     */
    public function fundTransfer(Request $request)
    {
        $request->validate([
            'selected_wallet' => 'required|in:main_wallet,fund_wallet',
            'tx_username'     => 'required|exists:users,username',
            'amount'          => 'required|numeric|min:1',
        ]);

        $current_user = Auth::user(); // Authenticated user

        // Get input values
        $amount = abs($request->amount);

        // Get user ID by username
        $user = User::where('username', $request->tx_username)->first();
        $u_id = $user->id;
        // Get current wallet balance
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = $request->selected_wallet;
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');

        $userWallet = UserWallet::firstOrNew(['u_code' => $u_id]); // Use firstOrNew to avoid duplicate queries
        $open_wallet  = $userWallet->$source_1 ?? 0;
        // Handle credit and debit operations
        if ($request->transaction_type == 'credit') {
            // If it's a credit, add the amount to the wallet
            $closing_wallet = $open_wallet + $amount;
            // Set the wallet value
            $userWallet->$source_1 = $closing_wallet;
            $remark = "$user->username received $amount from user";
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
            'wallet_type' => $request->selected_wallet,
            'tx_type' => 'fund_request',
            'debit_credit' => $request->transaction_type,
            'tx_u_code' => Auth::user()->id,
            'u_code' => $u_id,
            'amount' => $amount,
            'reason' => '',
            'date' => Carbon::now(),
            'status' => 0, // Assuming 0 is for pendding
            'open_wallet' => $open_wallet,
            'closing_wallet' => $closing_wallet,
            'remark' => $remark,
        ];

        // Insert transaction data
        $inserted = Transaction::create($transaction);

        return back()->with('success', 'Fund transferred successfully!');
    }
    /**
     * Show the fund request page.
     */
    public function fundRequest()
    {
        return view('user.fundrequesthistory'); // Ensure this view exists
    }

    /**
     * Get fund request data for DataTables.
     */
    public function getFundRequests(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('tx_type', 'fund_request')->where('u_code', $user->id)
            ->join('users', 'transaction.u_code', '=', 'users.id') // Join with users table
            ->select('transaction.*', 'users.username', 'users.name');

        if ($request->status) {
            switch ($request->status) {
                case 'approve':
                    $query->where('transaction.status', 1);
                    break;
                case 'pending':
                    $query->where('transaction.status', 0);
                    break;
                case 'cancel':
                    $query->where('transaction.status', 2);
                    break;
            }
        }

        return DataTables::of($query)
            // ->addColumn('actions', function ($row) {
            //     return view('user.partials.fundrequest-actions', compact('row'))->render();
            // })
            //->rawColumns(['actions']) // Ensure the actions column is not escaped
            ->make(true);
    }
    public function fundConvertform()
    {
        return view('user.fundconvert');
    }
    public function fundConvert(Request $request)
    {
        // Validate the request inputs
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'from_wallet' => 'required|in:roi_wallet,referral_wallet,autopool_wallet',
            'to_wallet' => 'required|in:fund_wallet',
        ]);

        $user = Auth::user();

        $u_Code = $user->id; // Assuming you use the ID as the user code
        $debit_amnt = abs($request->amount);
        $tx_amnt = $debit_amnt * 8 / 100; // Transaction charge (8%)
        $credit = $debit_amnt - $tx_amnt;
        $date = now();

        // Remarks
        $u_code_remark = "You convert $debit_amnt from {$request->from_wallet} to {$request->to_wallet}";
        $tx_u_code_remark = "You receive $credit from fund convert";



        // Prepare transactions
        $transactions = [
            [
                'wallet_type' => $request->from_wallet,
                'tx_type' => 'convert_send',
                'debit_credit' => 'debit',
                'tx_u_code' => $u_Code,
                'u_code' => $u_Code,
                'amount' => $debit_amnt,
                'tx_charge' => $tx_amnt,
                'date' => $date,
                'status' => 1,
                'remark' => $u_code_remark,
            ],
            [
                'wallet_type' => $request->to_wallet,
                'tx_type' => 'convert_receive',
                'debit_credit' => 'credit',
                'tx_u_code' => $u_Code,
                'u_code' => $u_Code,
                'amount' => $credit,
                'tx_charge' => 0,
                'date' => $date,
                'status' => 1,
                'remark' => $tx_u_code_remark,
            ],
        ];
        // Perform database operations within a transaction
        DB::beginTransaction();

        try {
            // Insert transactions
            foreach ($transactions as $transaction) {
                Transaction::create($transaction);
            }

            // Update user wallet balances

            $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]);
            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');

            //dd($reg_type); // From wallet Deduct money
            $source = $request->from_wallet;
            $source_1 = WalletType::where('slug', 'like', $source)
                ->where('wallet_type', 'income')
                ->where($reg_type, 1)
                ->value('wallet_column');
            $open_wallet  = $userWallet->$source_1 ?? 0;
            // Ensure sufficient balance in the source wallet
            if ($open_wallet < $debit_amnt) {
                return redirect()->back()->withErrors(['amount' => 'Insufficient balance in the selected wallet.']);
            }

            $closing_wallet = $open_wallet - $debit_amnt;
            // Set the wallet value
            $userWallet->$source_1 = $closing_wallet;

            //To wallet Recived money
            $source2 = $request->to_wallet;
            $source_2 = WalletType::where('slug', 'like', $source2)
                ->where('wallet_type', 'wallet')
                ->where($reg_type, 1)
                ->value('wallet_column');
            $to_wallet  = $userWallet->$source_2 ?? 0;

            $adding_wallet = $to_wallet + $credit;
            // Set the wallet value
            $userWallet->$source_2 = $adding_wallet;
            $userWallet->save();

            DB::commit(); // Commit transaction

            return redirect()->back()->with('success', "Convert successful. You converted {$debit_amnt}.");
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback transaction

            return redirect()->back()->withErrors(['error' => 'Something went wrong.']);
        }
    }
    public function fundhistory()
    {
        return view('user.fundconverthistory');
    }
    public function getFundhistory(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('tx_type', 'like', 'convert_%')->where('u_code', $user->id)
            ->join('users', 'transaction.u_code', '=', 'users.id') // Join with users table
            ->select('transaction.*', 'users.username', 'users.name');

        if ($request->status) {
            switch ($request->status) {
                case 'approve':
                    $query->where('transaction.status', 1);
                    break;
                case 'pending':
                    $query->where('transaction.status', 0);
                    break;
                case 'cancel':
                    $query->where('transaction.status', 2);
                    break;
            }
        }

        return DataTables::of($query)
            // ->addColumn('actions', function ($row) {
            //     return view('user.partials.fundrequest-actions', compact('row'))->render();
            // })
            //->rawColumns(['actions']) // Ensure the actions column is not escaped
            ->make(true);
    }
    public function topupform()
    {
        $user = Auth::user();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = 'main_wallet';
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
        $main_wallet  = $userWallet->$source_1 ?? 0;
        $source2 = 'fund_wallet';
        $source_2 = WalletType::where('slug', 'like', $source2)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $fund_wallet  = $userWallet->$source_2 ?? 0;
        return view('user.topup', compact('user', 'main_wallet', 'fund_wallet'));
    }
    public function topup(Request $request)
    {
        //dd($request);
        // Validation Rules
        $validatedData = $request->validate([
            'tx_username' => 'required|exists:users,username',
            'selected_pin' => 'required|exists:pin_details,id',
            'amount' => 'required|numeric|gt:0',
            'selected_wallet' => 'required',
            'otp_input1' => 'nullable|exists:otp,code', // Assuming OTP validation
        ]);

        try {

            // Retrieve User Details
            $txUsername = $request->tx_username;
            $txUser = User::where('username', $txUsername)->first();
            $userId = auth()->user()->id;
            $amount = $request->amount;
            $selectedWallet = $request->selected_wallet;
            $pinType = $request->selected_pin;
            $pinDetails = PinDetail::where('id', $pinType)->first();

            // Check wallet balance
            $fundAmount = UserWallet::where('u_code', $userId)->value('c2');
            if (!($fundAmount > $amount && $fundAmount > $pinDetails->pin_rate)) {
                return redirect()->back()->withErrors(['error' => "Invalid Amount! Minimum {$pinDetails->pin_rate} $ required."]);
            }

            // Determine transaction type (purchase or repurchase)
            $userDetails = User::find($txUser->id);
            $txType = ($userDetails->active_status == 1) ? 'repurchase' : 'purchase';
            $activeId = ($txType === 'purchase') ? User::max('active_id') + 1 : 0;
            $status = ($txType === 'purchase') ? 'yes' : 'no';

            if ($txType === 'repurchase') {
                return redirect()->back()->withErrors(['error' => 'Once Plan is Active, Please ReTopup.']);
            }

            // Update sponsor wallets
            $regType = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
            $uSponsorId = $userDetails->u_sponsor;
            if ($uSponsorId) {
                $source1 = WalletType::where('slug', 'active_directs')->where('wallet_type', 'team')->where($regType, 1)->value('wallet_column');
                $source2 = WalletType::where('slug', 'inactive_directs')->where('wallet_type', 'team')->where($regType, 1)->value('wallet_column');

                $sponsorWallet = UserWallet::firstOrNew(['u_code' => $uSponsorId]);
                $sponsorWallet->$source1 = ($sponsorWallet->$source1 ?? 0) + 1;
                $sponsorWallet->$source2 = ($sponsorWallet->$source2 ?? 0) - 1;
                $sponsorWallet->save();
            }

            // Create order
            $order = Order::create([
                'order_details' => $pinDetails->pkg_type,
                'u_code' => $txUser->id,
                'tx_user_id' => $userId,
                'tx_type' => $txType,
                'package_type' => $pinType,
                'order_amount' => $amount,
                'order_bv' => $amount,
                'pv' => $pinDetails->pin_value,
                'roi' => $pinDetails->roi,
                'status' => 1,
                'payout_id' => 1,
                'payout_status' => 0,
                'active_id' => $activeId,
            ]);

            // Deduct from wallet
            $walletColumn = WalletType::where('slug', $selectedWallet)->where('wallet_type', 'wallet')->where($regType, 1)->value('wallet_column');
            $userWallet = UserWallet::firstOrNew(['u_code' => $userId]);
            $userWallet->$walletColumn = ($userWallet->$walletColumn ?? 0) - $amount;
            $userWallet->save();

            // Update user activation status
            if ($status === 'yes') {
                $userDetails->update([
                    'my_package' => $amount,
                    'rank_id' => $pinDetails->id,
                    'active_id' => $activeId,
                    'active_status' => 1,
                    'active_date' => now(),
                ]);
            } else {
                $userDetails->update([
                    'my_package' => $amount,
                    'rank_id' => $pinDetails->id,
                    'retopup_status' => 1,
                    'retopup_date' => now(),
                ]);
            }

            // Create transaction record
            Transaction::create([
                'u_code' => $userId,
                'tx_u_code' => $txUser->id,
                'tx_type' => 'topup',
                'debit_credit' => 'debit',
                'wallet_type' => $selectedWallet,
                'amount' => $amount,
                'tx_charge' => 0, // Assuming no charge
                'date' => now(),
                'status' => 1,
                'remark' => auth()->user()->username . " topup $txUsername of amount $amount",
            ]);
            $this->direct_destribute($userDetails, $amount,  $register_fee = 0);
            return redirect()->back()->with('success', "$txUsername activated Package of amount $amount successfully!");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred.']);
        }
    }
    public function viptopupform()
    {
        $user = Auth::user();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = 'main_wallet';
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
        $main_wallet  = $userWallet->$source_1 ?? 0;
        $source2 = 'fund_wallet';
        $source_2 = WalletType::where('slug', 'like', $source2)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $fund_wallet  = $userWallet->$source_2 ?? 0;
        return view('user.viptopup', compact('user', 'main_wallet', 'fund_wallet'));
    }
    public function withdrawform()
    {
        $user = Auth::user();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = 'main_wallet';
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
        $main_wallet  = $userWallet->$source_1 ?? 0;
        $source2 = 'fund_wallet';
        $source_2 = WalletType::where('slug', 'like', $source2)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $fund_wallet  = $userWallet->$source_2 ?? 0;
        return view('user.withdraw', compact('user', 'main_wallet', 'fund_wallet'));
    }
    public function withdraw(Request $request)
    {
        try {
            // Validate request
            $validatedData = $request->validate([
                'selected_wallet' => 'nullable|string',
                'amount' => 'required|numeric|gt:0',
                'selected_address' => 'nullable|string|regex:/^0x[a-fA-F0-9]{40}$/'
            ]);

            $user = $request->user();
            $withdrawal_status = AdvancedInfo::where('label', 'like', 'withdrawal_status')->value('status');
            if ($withdrawal_status == 'off') {
                return redirect()->back()->withErrors(['error' => 'The withdrawal system is currently unavailable. Please try again later.']);
            }
            // Validate and calculate transaction details
            $source = $validatedData['selected_wallet'] ?? 'main_wallet';
            $amount = $validatedData['amount'];
            $transaction_fee = 10; // Transaction fee percentage
            $transactionFee = ($amount * $transaction_fee) / 100;
            $finalAmount = $amount - $transactionFee;
            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
            $withdrawal_limit = AdvancedInfo::where('label', 'like', 'min_withdrawal_limit')->value('value');

            $source_1 = WalletType::where('slug', 'like', $source)
                ->where('wallet_type', 'wallet')
                ->where($reg_type, 1)
                ->value('wallet_column');
            $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
            $source_1_amount = $userWallet->$source_1 ?? 0;
            // Validate user wallet balance
            if ($amount < $withdrawal_limit) {
                return redirect()->back()->withErrors(['error' => "Withdrawal Amount must be greater than $withdrawal_limit $."]);
            }
            if ($source_1_amount < $withdrawal_limit) {
                return redirect()->back()->withErrors(['error' => "Insufficient balance in the " . str_replace('_', ' ', $source) . ". Please try again later."]);
            }
            if (!$user->eth_address) {
                return redirect()->back()->withErrors(['error' => 'Withdrawal address must be added to the user.']);
            }
            // Deduct amount from user's wallet
            $userWallet->$source_1 = $source_1_amount - $amount;
            $userWallet->save();


            // Create transaction
            $transaction = Transaction::create([
                'u_code' => $user->id,
                'wallet_type' => $source,
                'amount' => $finalAmount,
                'tx_charge' => $transactionFee,
                'debit_credit' => 'debit',
                'tx_type' => 'withdrawal',
                'bank_details' => $validatedData['selected_address'] ?? $user->eth_address,
                'status' => 0, // Pending
            ]);

            return redirect()->back()->with('success', 'Withdrawal request added successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'An unexpected error occurred.']);
        }
    }
    public function upgradeform()
    {
        $user = Auth::user();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $source = 'main_wallet';
        $source_1 = WalletType::where('slug', 'like', $source)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]); // Use firstOrNew to avoid duplicate queries
        $main_wallet  = $userWallet->$source_1 ?? 0;
        $source2 = 'fund_wallet';
        $source_2 = WalletType::where('slug', 'like', $source2)
            ->where('wallet_type', 'wallet')
            ->where($reg_type, 1)
            ->value('wallet_column');
        $fund_wallet  = $userWallet->$source_2 ?? 0;
        return view('user.upgrade', compact('user', 'main_wallet', 'fund_wallet'));
    }
    public function withdrawhistory()
    {
        return view('user.withdrawhistory');
    }
    public function getwithdrawhistory(Request $request)
    {
        $user = Auth::user();
        $query = Transaction::where('tx_type', 'like', 'withdrawal%')->where('u_code', $user->id)
            ->join('users', 'transaction.u_code', '=', 'users.id') // Join with users table
            ->select('transaction.*', 'users.username', 'users.name');

        if ($request->status) {
            switch ($request->status) {
                case 'approve':
                    $query->where('transaction.status', 1);
                    break;
                case 'pending':
                    $query->where('transaction.status', 0);
                    break;
                case 'cancel':
                    $query->where('transaction.status', 2);
                    break;
            }
        }

        return DataTables::of($query)
            // ->addColumn('actions', function ($row) {
            //     return view('user.partials.fundrequest-actions', compact('row'))->render();
            // })
            //->rawColumns(['actions']) // Ensure the actions column is not escaped
            ->make(true);
    }
    function direct_destribute($user, $amount, $register_fee = 0)
    {
        $code = $user->u_sponsor;
        $ben_from = $user->id;
        $source = 'direct';
        $name  = $user->name;
        $username = $user->username;
        $l = 1;
        $amount = $register_fee ?? 10;
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $plan = PinDetail::where('status', 1)->first();
        if ($plan) {
            $col_nm = WalletType::where('slug', 'like', 'active_directs')->where('wallet_type', 'like', 'team')->where($reg_type, 1)->value('wallet_column');
            $direct_count = UserWallet::where('u_code', $code)->value($col_nm);

            // Determine income percentage based on direct referral count
            if ($direct_count >= 1 && $direct_count <= 10) {
                $level_percentage = 0.10;
            } elseif ($direct_count >= 11 && $direct_count <= 20) {
                $level_percentage = 0.20;
            } elseif ($direct_count >= 21 && $direct_count <= 30) {
                $level_percentage = 0.30;
            } elseif ($direct_count >= 31 && $direct_count <= 40) {
                $level_percentage = 0.40;
            } else { // 41 and above
                $level_percentage = 0.50;
            }

            $payable_amount = $amount * $level_percentage;

            if ($payable_amount > 0 && $code != '') {
                $transaction = [
                    'tx_u_code' => $ben_from,
                    'u_code' => $code,
                    'tx_type' => 'income',
                    'source' => $source,
                    'debit_credit' => 'credit',
                    'amount' => $payable_amount,
                    'tx_charge' => 0,
                    'date' => Carbon::today(),
                    'wallet_type' => 'main_wallet',
                    'status' => 1,
                    'payout_id' => 0,
                    'tx_record' => $l,
                    'remark' => "Received $source income of amount $payable_amount from $name ($username) from level $l",
                ];

                $inserted = Transaction::create($transaction);
                if (@$inserted) {
                    // Fetch wallet column names based on conditions
                    $source_1 = WalletType::where('slug', 'like', $source)
                        ->where('wallet_type', 'income')
                        ->where($reg_type, 1)
                        ->value('wallet_column');

                    $source_2 = WalletType::where('slug', 'like', 'main_wallet')
                        ->where('wallet_type', 'wallet')
                        ->where($reg_type, 1)
                        ->value('wallet_column');

                    // Fetch current wallet amounts
                    $userWallet = UserWallet::firstOrNew(['u_code' => $code]); // Use firstOrNew to avoid duplicate queries
                    $source_1_amount = $userWallet->$source_1 ?? 0;
                    $source_2_amount = $userWallet->$source_2 ?? 0;

                    // Update wallet amounts
                    $userWallet->$source_1 = $source_1_amount + $payable_amount;
                    $userWallet->$source_2 = $source_2_amount + $payable_amount;

                    // Save or update record
                    $userWallet->save();
                }
            }
        }
    }
    public function orderhistory()
    {
        return view('user.orderhistory');
    }
    public function getorderhistory(Request $request)
    {
        $user = Auth::user();
        $query = Order::where('orders.status',  '1')->where('u_code', $user->id)
            ->join('users', 'orders.u_code', '=', 'users.id') // Join with users table
            ->join('pin_details', 'orders.package_type', '=', 'pin_details.id') // Join with users table
            ->select('orders.*', 'pin_details.pin_type', 'users.username', 'users.name');

        if ($request->status) {
            switch ($request->status) {
                case 'approve':
                    $query->where('orders.status', 1);
                    break;
                case 'pending':
                    $query->where('orders.status', 0);
                    break;
                case 'cancel':
                    $query->where('orders.status', 2);
                    break;
            }
        }

        return DataTables::of($query)
            // ->addColumn('actions', function ($row) {
            //     return view('user.partials.fundrequest-actions', compact('row'))->render();
            // })
            //->rawColumns(['actions']) // Ensure the actions column is not escaped
            ->make(true);
    }
    public function reporthistory()
    {
        $user = Auth::user();
        return view('user.reporthistory', compact('user'));
    }
}