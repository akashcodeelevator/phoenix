<?php

namespace App\Http\Controllers;

use App\Models\AdvancedInfo;
use App\Models\FundRequest;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\PinDetail;
use App\Models\PinHistory;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletAddress;
use App\Models\WalletType;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlanController extends Controller
{
    //
    /**
     * @OA\Get(
     *     path="/api/v1/checksponsor",
     *     tags={"Package"},
     *     summary="Check if a sponsor is valid",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="The ID of the sponsor to validate",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Sponsor is valid"),
     *     @OA\Response(response="404", description="Sponsor not found"),
     *     @OA\Response(response="422", description="Invalid input"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function checksponsor(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string|exists:users,username',
            ]);

            // Correct the query to fetch the user
            $user = User::where('username', $request->username)->first();

            // Check if the user was found
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 200);
            }

            // Retrieve the user's name
            $ssuser_name = $user->name;
            return response()->json(['status' => true, 'user' => ['id' => $user->id, 'name' => $user->name, 'username' => $user->username], 'message' => 'User is valid.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'User not found. '], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/pin-details",
     *     tags={"Package"},
     *     summary="Fetch Package where status is 1",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getActivePinDetails(Request $request)
    {
        try {
            // Fetch PinDetails where status is 1
            $pinDetails = PinDetail::where('status', 1)
                ->get(['id', 'pin_rate', 'package_name'])
                ->toArray();

            if (empty($pinDetails)) {
                return response()->json(['status' => false, 'message' => 'No PinDetails found.'], 200);
            }
            $userId = $request->user()->id;
            $orderStatus = Order::where('u_code', $userId)
                ->get(['status', 'package_type'])
                ->toArray();
            return response()->json(['status' => true, 'pinDetails' => $pinDetails, 'order' => $orderStatus], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/investment",
     *     tags={"Package"},
     *     summary="Process investment for a user",
     *     @OA\Parameter(
     *         name="tx_username",
     *         in="query",
     *         required=true,
     *         description="Username of the user making the investment",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="selected_pin",
     *         in="query",
     *         required=true,
     *         description="Selected pin for the investment",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         description="Amount of the investment",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *      @OA\Parameter(
     *         name="country_name",
     *         in="query",
     *         required=true,
     *         description="Enter Country Name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="otp_input1",
     *         in="query",
     *         required=false,
     *         description="OTP for validation",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="Investment successful"),
     *     @OA\Response(response="400", description="Validation failed"),
     *     @OA\Response(response="404", description="User not found"),
     *     @OA\Response(response="500", description="Server error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function investment(Request $request)
    {
        $userId = $request->user()->id;
        // Validation Rules
        $validator = Validator::make($request->all(), [
            'tx_username' => 'required|exists:users,username',
            'selected_pin' => 'required|exists:pin_details,id',
            'amount' => 'required',
            'country_name' => 'required',
            'otp_input1' => 'nullable|exists:otp,code', // Assuming OTP validation
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()], 200);
        }

        // Retrieve User Details
        $txUsername = $request->tx_username;
        $txUser = User::where('username', $txUsername)->first();

        if (!$txUser) {
            return response()->json(['status' => false, 'message' => 'User not found.'], 200);
        }
        $amount = $request->amount;
        $country_name = $request->country_name ?? '';
        $userDetails = User::find($txUser->id);
        $pinType = $request->selected_pin;
        $pinDetails = PinDetail::where('id', $pinType)->first();
        $fundamount = UserWallet::where('u_code', $userId)->value('c2');
        $register_fee = 0;
        if (!($fundamount > $request->amount && $fundamount > $pinDetails->pin_rate)) {
            return response()->json(['status' => false, 'message' => 'Invalid Amount! Amount Minimum ' . $pinDetails->pin_rate . ' $ required.'], 200);
        }


        // Determine the type of transaction (purchase or repurchase)
        if ($userDetails->active_status == 1) {
            return response()->json(['status' => false, 'message' => 'Once Plan is Active Please You can ReTopup'], 200);
            $txType = 'repurchase';
            $activeId = 0;
            $sts = 'no';
            $latestPkg = $userDetails->my_package + $amount;
        } else {
            $txType = 'purchase';
            $activeId = User::max('active_id') + 1;
            $sts = 'yes';
        }
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        $u_id = $userDetails->u_sponsor;
        if ($u_id) {
            //active_directs  
            $source_1 = WalletType::where('slug', 'like', 'active_directs')
                ->where('wallet_type', 'team')
                ->where($reg_type, 1)
                ->value('wallet_column');
            //inactive_directs            
            $source_2 = WalletType::where('slug', 'like', 'inactive_directs')
                ->where('wallet_type', 'team')
                ->where($reg_type, 1)
                ->value('wallet_column');
            // Fetch current wallet counts
            $userWallet = UserWallet::firstOrNew(['u_code' => $u_id]); // Use firstOrNew to avoid duplicate queries
            //active_directs
            $source_1_counts = $userWallet->$source_1 ?? 0;
            //inactive_directs
            $source_2_counts = $userWallet->$source_2 ?? 0;

            // Update wallet countss
            $no_count = 1;
            //active_directs
            $userWallet->$source_1 = $source_1_counts + $no_count;
            //inactive_directs
            $userWallet->$source_2 = $source_2_counts - $no_count;
            // Save or update record
            $userWallet->save();
        }
        // Create the order
        $order = Order::create([
            'order_details' => $pinDetails->pkg_type,
            'u_code' => $txUser->id,
            'tx_user_id' => $userId,
            'tx_type' => $txType,
            'package_type' => $pinType,
            'order_amount' => $amount - $register_fee,
            'order_bv' => $amount,
            'pv' => $pinDetails->pin_value,
            'roi' => $pinDetails->roi,
            'status' => 1,
            'payout_id' => 1, // Assuming payout ID is predefined
            'payout_status' => 0,
            'active_id' => $activeId,
            'country_name' => $country_name,
        ]);
        if ($userId) {
            //main_wallet  
            $source_1 = WalletType::where('slug', 'like', 'fund_wallet')
                ->where('wallet_type', 'wallet')
                ->where($reg_type, 1)
                ->value('wallet_column');

            // Fetch current wallet counts
            $userWallet1 = UserWallet::firstOrNew(['u_code' => $userId]); // Use firstOrNew to avoid duplicate queries
            //main_wallet
            $source_1_amount = $userWallet1->$source_1 ?? 0;

            // Update wallet amount
            //inactive_directs
            $userWallet1->$source_1 = $source_1_amount - $amount;
            // Save or update record
            $userWallet1->save();
        }
        // Update user data if first-time activation
        if ($sts == 'yes') {
            $userDetails->update([
                'my_package' => $amount,
                'rank_id' => $pinDetails->id,
                'active_id' => $activeId,
                'active_status' => 1,
                'active_date' => Carbon::now(),
            ]);
        } else {
            $userDetails->update([
                'my_package' => $amount,
                'rank_id' => $pinDetails->id,
                'retopup_status' => 1,
                'retopup_date' => Carbon::now(),
            ]);
        }
        $selected_wallet =  WalletType::where('wallet_column', 'like', 'c2')->where('wallet_type', 'like', 'wallet')->value('slug');
        // Insert a new transaction record
        $transaction = Transaction::create([
            'u_code' => $userId,
            'tx_u_code' => $txUser->id,
            'tx_type' => 'topup',
            'debit_credit' => 'debit',
            'wallet_type' => $selected_wallet,
            'amount' => $amount,
            'tx_charge' => $register_fee,
            'date' => Carbon::now(),
            'status' => 1,
            'remark' => auth()->user()->username . " topup $txUsername of amount $amount",
        ]);

        // Update pin usage if applicable
        if ($pinType) {
            $pinHistory = PinHistory::create([
                'user_id' => $userId,
                'debit' => 1,
                'prev_pin' => 1, // Assuming number of pins before transaction
                'curr_pin' => 0, // Assuming number of pins after transaction
                'pin_type' => $pinDetails->pin_type,
                'tx_type' => 'debit',
                'remark' => auth()->user()->name . " topup $txUsername",
            ]);
        }
        //$this->direct_destribute($userDetails, $amount, $register_fee);
        // Return success response
        return response()->json([
            'status' => true,
            'message' => "$txUsername activated Package of amount $amount successfully!",
            'order_id' => $order->id
        ], 200);
    }
    function direct_destribute($user, $amount, $register_fee)
    {
        $code = $user->u_sponsor;
        $ben_from = $user->id;
        $source = 'direct';
        $name  = $user->name;
        $username = $user->username;
        $l = 1;
        $amount = $register_fee;
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
    /**
     * @OA\Get(
     *     path="/api/v1/fund-details",
     *     tags={"Fund"},
     *     summary="Fetch payment methods where status is 1",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="404", description="No payment methods found"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getFundDetails(Request $request)
    {
        try {
            // Fetch payment methods where status is 1
            $paymentMethods = PaymentMethod::where('status', 1)
                ->get(['id', 'parent_method', 'slug', 'name', 'image', 'address'])
                ->map(function ($paymentMethod) {
                    $paymentMethod->image = asset('storage/qr/' . $paymentMethod->image);
                    return $paymentMethod;
                })
                ->toArray();
            $user = $request->user();
            $wallet = UserWallet::where('u_code', $user->id)->first();
            $walletColumns = ['c1', 'c2', 'c3', 'c4', 'c5', 'c6', 'c7', 'c8', 'c9', 'c10', 'c11', 'c12', 'c13', 'c14', 'c15', 'c16', 'c17', 'c18', 'c19', 'c20', 'c21', 'c22', 'c23', 'c24', 'c25', 'c26', 'c27'];
            $walletdata = [];

            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');

            // Iterate over each column and replace with the wallet name from the wallet_types table
            foreach ($walletColumns as $in => $column) {

                // Find the wallet type name based on the column value
                $walletType = WalletType::where('wallet_column', 'like', $column)->where('wallet_type', 'like', 'wallet')->where($reg_type, 1)->first();
                //dd($walletType);
                if ($walletType) {
                    // Store the wallet name and price for the corresponding column
                    $walletdata[$in]['name'] = $walletType->name; // Replace the column with the wallet name
                    $walletdata[$in]['slug'] = $walletType->slug;
                    $walletdata[$in]['price'] = @$wallet->{$column} ?? 0; // Store the price in the walletdata array
                }
            }
            if (empty($paymentMethods)) {
                return response()->json(['status' => false, 'message' => 'No payment methods found.'], 200);
            }
            return response()->json(['status' => true, 'payment_methods' => $paymentMethods, 'wallet' => $walletdata], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/fund-request",
     *     tags={"Fund"},
     *     summary="Submit a fund request",
     *     @OA\Parameter(
     *         name="address",
     *         in="query",
     *         required=true,
     *         description="Crypto wallet address",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="payment_type",
     *         in="query",
     *         required=true,
     *         description="Type of payment",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         description="Amount to request",
     *         @OA\Schema(type="number", format="float")
     *     ),
     *     @OA\Parameter(
     *         name="utr_number",
     *         in="query",
     *         required=true,
     *         description="UTR (Unique Transaction Reference) number",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="payment_slip",
     *         in="query",
     *         required=true,
     *         description="Uploaded image of the payment slip",
     *         @OA\Schema(type="string", format="binary")
     *     ),
     *     @OA\Response(response="200", description="Fund request submitted successfully"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function addFundRequest(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'address' => 'required|string',
                'payment_type' => 'required|string',
                'amount' => 'required|numeric|min:0',
                'utr_number' => 'required|string',
                'payment_slip' => 'required|file|mimes:jpg,jpeg,png|max:2048', // Ensure file is an image and max size is 2MB
            ]);

            // Handle file upload
            if ($request->hasFile('payment_slip')) {

                $filePath = $request->file('payment_slip')->store('public/payment_slip');
                if ($filePath) {
                    $paymentSlipUrl = asset('storage/' . str_replace('public/', '', $filePath));
                } else {
                    $paymentSlipUrl = '';
                }
            } else {
                return response()->json(['status' => false, 'message' => 'Payment slip upload failed.'], 200);
            }

            // Get authenticated user's profile
            $user = $request->user();

            // Prepare data for the transaction
            $transaction = [
                'payment_slip' => $paymentSlipUrl,
                'wallet_type' => 'fund_wallet',
                'tx_type' => 'fund_request',
                'payment_type' => $validated['payment_type'],
                'cripto_type' => $validated['address'],
                'cripto_address' => $validated['utr_number'],
                'debit_credit' => 'credit',
                'u_code' => $user->id,
                'amount' => abs($validated['amount']),
                'date' => now(),
                'status' => 0,
                'remark' => "{$user->name} ({$user->username}) fund request {$validated['amount']}",
            ];

            // Insert the transaction into the database
            $inserted = Transaction::create($transaction);

            if ($inserted) {
                return response()->json([
                    'status' => true,
                    'message' => "Fund request submitted successfully. Amount: {$validated['amount']}",
                ], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong.'], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred.'], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/fund-transfer",
     *     tags={"Fund"},
     *     summary="Transfer funds between wallets",
     *     @OA\Parameter(
     *         name="from_wallet",
     *         in="query",
     *         required=false,
     *         description="Source wallet slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="to_wallet",
     *         in="query",
     *         required=false,
     *         description="Destination wallet slug",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="Recipient username",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="amount",
     *         in="query",
     *         required=true,
     *         description="Amount to transfer",
     *         @OA\Schema(type="number", format="float", minimum=0.01)
     *     ),
     *     @OA\Response(response="200", description="Fund transfer successful"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function addFundTransfer(Request $request)
    {
        try {
            // Validate request
            $validated = $request->validate([
                //'from_wallet' => 'required|string|in:main_wallet,fund_wallet',
                //'to_wallet' => 'required|string|in:main_wallet,fund_wallet|different:from_wallet',
                'username' => 'required|string|exists:users,username',
                'amount' => 'required|min:0',
            ]);

            // Get authenticated user
            $user = $request->user();

            // Find recipient user
            $recipient = User::where('username', $validated['username'])->first();
            if (!$recipient) {
                return response()->json(['status' => false, 'message' => 'Recipient user not found.'], 200);
            }
            $validated['from_wallet'] = $validated['from_wallet'] ?? 'fund_wallet';
            $validated['to_wallet'] = $validated['from_wallet'] ?? 'fund_wallet';
            $userWallet = UserWallet::firstOrNew(['u_code' => $user->id]);
            $recipientWallet = UserWallet::firstOrNew(['u_code' => $recipient->id]);
            $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
            // Fetch balances for the authenticated user's wallets
            $source_1 = WalletType::where('slug', 'like', $validated['from_wallet'])
                ->where('wallet_type', 'wallet')
                ->where($reg_type, 1)
                ->value('wallet_column');
            $source_2 = WalletType::where('slug', 'like', $validated['to_wallet'])
                ->where('wallet_type', 'wallet')
                ->where($reg_type, 1)
                ->value('wallet_column');
            // Fetch current wallet amounts
            $fromWalletBalance = $userWallet->$source_1 ?? 0;
            $toWalletBalance = $userWallet->$source_2 ?? 0;

            if ($fromWalletBalance < $validated['amount']) {
                return response()->json(['status' => false, 'message' => 'Insufficient balance in source wallet.'], 200);
            }

            // Perform the fund transfer
            DB::beginTransaction();

            // Deduct from source wallet
            $userWallet->$source_1 = $fromWalletBalance - $validated['amount'];

            // Add to destination wallet
            if ($recipient->id == $user->id) {
                $userWallet->$source_2 = $toWalletBalance + $validated['amount'];
            } else {
                $toREWalletBalance = $recipientWallet->$source_2 ?? 0;
                $recipientWallet->$source_2 = $toREWalletBalance + $validated['amount'];
                $recipientWallet->save();
            }

            // Save or update record
            $userWallet->save();
            // Log transactions for both users
            $transactions = [
                [
                    'u_code' => $user->id,
                    'wallet_type' => $validated['from_wallet'],
                    'tx_type' => 'fund_transfer',
                    'debit_credit' => 'debit',
                    'amount' => $validated['amount'],
                    'date' => now(),
                    'status' => 1,
                    'remark' => "Transfer to {$recipient->username} ({$validated['to_wallet']})",
                ],
                [
                    'u_code' => $recipient->id,
                    'wallet_type' => $validated['to_wallet'],
                    'tx_type' => 'fund_transfer',
                    'debit_credit' => 'credit',
                    'amount' => $validated['amount'],
                    'date' => now(),
                    'status' => 1,
                    'remark' => "Received from {$user->username} ({$validated['from_wallet']})",
                ],
            ];

            Transaction::insert($transactions);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Fund Transfer successful. Transferred {$recipient->username} :  {$validated['amount']} from {$user->username} : " . str_replace('_', ' ', $validated['from_wallet']) . " to " . str_replace('_', ' ', $validated['to_wallet']) . ".",
            ], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => 'Fund Transfer Failed.'], 200);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/v1/report",
     *     summary="Get transaction report with filters",
     *     tags={"Transactions"},
     *     @OA\Parameter(
     *         name="tx_type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by transaction type"
     *     ),
     *     @OA\Parameter(
     *         name="debit_credit",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by debit or credit (debit/credit)"
     *     ),
     *     @OA\Parameter(
     *         name="wallet_type",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Filter by wallet type"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for filtering"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for filtering"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction report",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="amount", type="number"),
     *                 @OA\Property(property="date", type="string"),
     *                 @OA\Property(property="remark", type="string"),
     *                 @OA\Property(property="status", type="string"),
     *                 @OA\Property(property="u_code", type="string"),
     *                 @OA\Property(property="wallet_type", type="string"),
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request"),
     * )
     */
    public function getReport(Request $request)
    {
        $query = Transaction::query();

        // Apply filters
        if ($request->has('tx_type')) {
            $query->where('tx_type', $request->tx_type);
        }
        if ($request->has('debit_credit')) {
            $query->where('debit_credit', $request->debit_credit);
        }
        if ($request->has('wallet_type')) {
            $query->where('wallet_type', $request->wallet_type);
        }
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }
        $userId = $request->user()->id;
        $query->where('u_code', $userId);
        // Select fields and paginate
        $transactions = $query->select(['amount', 'debit_credit', 'date', 'remark', 'status', 'u_code', 'wallet_type'])
            ->paginate(1000);

        return response()->json($transactions, 200);
    }
    public function roi()
    {
        $source = "roi";
        $admin_per = PinDetail::where('status', 1)->value('roi');
        $currentDate = Carbon::now()->toDateString();

        $all_order =  $order = Order::where('status', 1)->get();
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
        foreach ($all_order as $order) {
            $userid = $order->u_code;
            $roi_incomes = $order->order_amount * $admin_per / 100;
            $info_u = User::where('id', $userid)->first();
            $name = $info_u->name;
            $username = $info_u->username;
            $userWallet = UserWallet::firstOrNew(['u_code' => $userid]);
            $lastCronRun = Carbon::parse($userWallet->last_cron_run)->toDateString();
            if ($lastCronRun !== $currentDate) {
                $transaction = [
                    'u_code' => $userid,
                    'tx_type' => 'income',
                    'source' => $source,
                    'debit_credit' => 'credit',
                    'amount' => $roi_incomes,
                    'tx_charge' => 0,
                    'date' => Carbon::today(),
                    'wallet_type' => 'roi_wallet',
                    'status' => 1,
                    'payout_id' => 0,
                    'tx_record' => $order->id,
                    'remark' => "Received $source income of amount $roi_incomes from $name ($username)",
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
                    $source_1_amount = $userWallet->$source_1 ?? 0;
                    $source_2_amount = $userWallet->$source_2 ?? 0;

                    // Update wallet amounts
                    $userWallet->$source_1 = $source_1_amount + $roi_incomes;
                    $userWallet->$source_2 = $source_2_amount + $roi_incomes;
                    $userWallet->last_cron_run = $currentDate;
                    // Save or update record
                    $userWallet->save();
                }
            } else {
                // Skip updating if the cron has already run today
                \Log::info("Cron already ran for user ID: $userid today.");
            }
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/teamdirect",
     *     summary="Get team list for a Team",
     *     tags={"Team"},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer"),
     *         description="Filter by user status (1 for active, 0 for inactive)"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Start date for filtering"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="End date for filtering"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Team list",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(
     *                 @OA\Property(property="name", type="string"),
     *                 @OA\Property(property="mobile", type="string"),
     *                 @OA\Property(property="username", type="string"),
     *                 @OA\Property(property="email", type="string"),
     *                 @OA\Property(property="status", type="integer"),
     *                 @OA\Property(property="date", type="string", format="date"),
     *             )),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=400, description="Invalid request"),
     * )
     */
    public function getTeam(Request $request)
    {
        $user = $request->user();

        $query = User::where('u_sponsor', $user->id);

        $totalTeam = $query->count() ?? 0;; // Count all users where u_sponsor matches
        $activeTeam = $query->where('active_status', 1)->count() ?? 0;; // Count users with active_status = 1
        $inactiveTeam = $query->where('active_status', 0)->count() ?? 0;; // Count users with active_status = 0

        // Apply filters
        if ($request->has('status')) {
            $query->where('active_status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Select fields and paginate
        $team = $query->select(['name', 'mobile', 'username', 'email', 'active_status', 'created_at as date'])
            ->paginate(1000);

        $roi_income = UserWallet::where('u_code', $user->id)->value('c3') ?? 0;
        $direct_income = UserWallet::where('u_code', $user->id)->value('c8') ?? 0;

        $status_team = ['total_team' => $totalTeam, 'active_team' => $activeTeam, 'inactive_team' => $inactiveTeam, 'direct_income' => $direct_income, 'roi_income' => $roi_income];

        return response()->json(['status' => true, 'other_count' => $status_team, 'team' => $team], 200);
    }
    /**
     * @OA\Post(
     *     path="/api/v1/updateWalletAddress",
     *     tags={"Withdraw"},
     *     summary="Update ETH address for a user",
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="The username of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="eth_address",
     *         in="query",
     *         required=true,
     *         description="New ETH address to be added or updated",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response="200", description="ETH address updated successfully"),
     *     @OA\Response(response="404", description="User not found"),
     *     @OA\Response(response="422", description="Validation error"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function updateWalletAddress(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'eth_address' => 'required|string|unique:users,eth_address',
            ]);
            $u_data = $request->user();
            // Retrieve user by username
            $user = User::where('id', $u_data->id)->first();

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 200);
            }
            if ($user->eth_address == '') {
                // Update ETH address
                $user->eth_address = $validatedData['eth_address'];
                $user->save();
                return response()->json(['status' => true, 'user' => $user, 'message' => 'ETH address updated successfully.'], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'ETH address already added.'], 200);
            }
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => 'Validation error: ' . $e->getMessage()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred. Please try again later.'], 200);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/fundWithdraw",
     *     tags={"Withdraw"},
     *     summary="Submit a fund withdrawal request",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"selected_wallet", "amount"},
     *             @OA\Property(property="selected_wallet", type="string", description="Wallet type (e.g., main_wallet)"),
     *             @OA\Property(property="amount", type="number", format="float", description="Withdrawal amount"),
     *             @OA\Property(property="selected_address", type="string", description="Recipient crypto address (if applicable)")
     *         )
     *     ),
     *     @OA\Response(response="200", description="Withdrawal request processed successfully"),
     *     @OA\Response(response="400", description="Validation error"),
     *     @OA\Response(response="500", description="Unexpected server error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function fundWithdraw(Request $request)
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
                return response()->json(['status' => false,  'message' => "The withdrawal system is currently unavailable. Please try again later."], 200);
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
                return response()->json(['status' => false, 'message' => "Withdrawal Amount must be greater than $withdrawal_limit $."], 200);
            }
            if ($source_1_amount < $withdrawal_limit) {
                return response()->json(['status' => false, 'message' => "Insufficient balance in the " . str_replace('_', ' ', $source) . ". Please try again later."], 200);
            }
            if (!$user->eth_address) {
                return response()->json(['status' => false, 'message' => "Withdrawal address must be add in user."], 200);
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

            return response()->json(['status' => true, 'message' => 'Withdrawal request added successfully.'], 200);
        } catch (ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'An unexpected error occurred.'], 200);
        }
    }
    /**
     * Get Fund Request History based on status.
     *
     * @OA\Get(
     *     path="/api/v1/fund-request-history",
     *     tags={"Fund"},
     *     summary="Get fund request history",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (approve, pending, cancel)",
     *         @OA\Schema(type="string", enum={"approve", "pending", "cancel"})
     *     ),
     *     @OA\Response(response="200", description="Fund request history retrieved successfully"),
     *     @OA\Response(response="404", description="No fund request found"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getFundRequestHistory(Request $request)
    {
        try {
            // Validate the status parameter if provided
            $request->validate([
                'status' => 'nullable|in:Approve,Pending,Cancel',
            ]);
            $userId = $request->user()->id;
            // Build the query for transactions
            $transactions = Transaction::where('tx_type', 'like', 'fund_request')
                ->join('users', 'transaction.u_code', '=', 'users.id') // Join with users table
                ->where('users.id', $userId)
                ->select(
                    'transaction.id',
                    'transaction.u_code',
                    'transaction.tx_type',
                    'transaction.debit_credit',
                    'transaction.wallet_type',
                    'transaction.amount',
                    'transaction.tx_charge',
                    'transaction.created_at',
                    'transaction.remark',
                    'users.username',
                    'users.name',
                    DB::raw("CASE 
                WHEN transaction.status = 0 THEN 'Pending'
                WHEN transaction.status = 1 THEN 'Approve'
                WHEN transaction.status = 2 THEN 'Cancel'
                ELSE 'Unknown' 
            END AS status")
                );

            // Apply status filter if provided
            if ($request->filled('status')) {
                $status = $request->status;

                if ($status == 'Approve') {
                    $transactions->where('transaction.status', 1); // Approved
                } elseif ($status == 'Pending') {
                    $transactions->where('transaction.status', 0); // Pending
                } elseif ($status == 'Cancel') {
                    $transactions->where('transaction.status', 2); // Cancelled
                }
            }

            // Get the transactions
            $transaction = $transactions->get();

            // Check if any transactions found
            if ($transaction->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No fund request found.'], 200);
            }

            // Return successful response with transaction data
            return response()->json([
                'status' => true,
                'message' => 'Fund request history retrieved successfully',
                'fund' => $transaction
            ], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 200);
        }
    }
    /**
     * Get Withdraw Request History based on status.
     *
     * @OA\Get(
     *     path="/api/v1/withdraw-request-history",
     *     tags={"Withdraw"},
     *     summary="Get withdraw request history",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         required=false,
     *         description="Filter by status (Approve, Pending, Cancel)",
     *         @OA\Schema(type="string", enum={"Approve", "Pending", "Cancel"})
     *     ),
     *     @OA\Response(response="200", description="Withdraw request history retrieved successfully"),
     *     @OA\Response(response="404", description="No withdraw request found"),
     *     @OA\Response(response="500", description="Unexpected error"),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function getWithdrawRequestHistory(Request $request)
    {
        try {
            // Validate the status parameter if provided
            $request->validate([
                'status' => 'nullable|in:Approve,Pending,Cancel',
            ]);
            $userId = $request->user()->id;
            // Build the query for transactions
            $transactions = Transaction::where('tx_type', 'like', 'withdrawal')
                ->join('users', 'transaction.u_code', '=', 'users.id') // Join with users table
                ->where('users.id', $userId)
                ->select(
                    'transaction.id',
                    'transaction.u_code',
                    'transaction.tx_type',
                    'transaction.debit_credit',
                    'transaction.wallet_type',
                    'transaction.amount',
                    'transaction.tx_charge',
                    'transaction.created_at',
                    'transaction.remark',
                    'users.username',
                    'users.name',
                    DB::raw("CASE 
                WHEN transaction.status = 0 THEN 'Pending'
                WHEN transaction.status = 1 THEN 'Approve'
                WHEN transaction.status = 2 THEN 'Cancel'
                ELSE 'Unknown' 
            END AS status")
                );

            // Apply status filter if provided
            if ($request->filled('status')) {
                $status = $request->status;

                if ($status == 'Approve') {
                    $transactions->where('transaction.status', 1); // Approved
                } elseif ($status == 'Pending') {
                    $transactions->where('transaction.status', 0); // Pending
                } elseif ($status == 'Cancel') {
                    $transactions->where('transaction.status', 2); // Cancelled
                }
            }

            // Get the transactions
            $transaction = $transactions->get();

            // Check if any transactions found
            if ($transaction->isEmpty()) {
                return response()->json(['status' => false, 'message' => 'No withdraw request found.'], 200);
            }

            // Return successful response with transaction data
            return response()->json([
                'status' => true,
                'message' => 'Withdraw request history retrieved successfully',
                'withdraw' => $transaction
            ], 200);
        } catch (\Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'status' => false,
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 200);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/verify-transaction",
     *     tags={"Transactions"},
     *     summary="Verify a USDT transaction",
     *     description="This endpoint verifies a USDT transaction by its hash, checks its validity, and marks it as successful if verified.",
     *     @OA\Parameter(
     *         name="txHash",
     *         in="query",
     *         required=true,
     *         description="Transaction hash to be verified",
     *         @OA\Schema(type="string", example="0x123abc...transactionHash")
     *     ),
     *      @OA\Parameter(
     *         name="from_address",
     *         in="query",
     *         required=true,
     *         description="Transaction From Wallet Address",
     *         @OA\Schema(type="string", example="0x123abc...Address")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Transaction verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Transaction is verified and successful.")
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     */
    public function verifyTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'txHash' => 'required',
            'from_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->errors()->first()], 200);
        }

        $txHash = $request->input('txHash');
        $amount = $request->input('amount', 0.1);
        $currency_code = $request->input('currency_code', 'USD');
        $paymentMethods = PaymentMethod::where('status', 1)
            ->get(['id', 'parent_method', 'slug', 'name', 'image', 'address'])
            ->map(function ($paymentMethod) {
                $paymentMethod->image = asset('storage/qr/' . $paymentMethod->image);
                return $paymentMethod;
            })->toArray();
        $adminWalletAddress = isset($paymentMethods[0]['address']) && !empty($paymentMethods[0]['address']) ? $paymentMethods[0]['address'] : env('ADMIN_WALLET_ADDRESS');
        if (!$adminWalletAddress) {
            return response()->json(['status' => false, 'message' => 'Admin wallet address is not configured.']);
        }
        $tokenContractAddress = env('TOKEN_CONTRACT_ADDRESS');
        $bscApiKey = env('BSC_API_KEY');
        $baseUrl = env('BSC_API_URL');
        $user = $request->user();
        $address =  $user->eth_address; //$request->input('from_address');

        try {
            if ($address != $request->input('from_address')) {
                return response()->json(['status' => false, 'message' => 'Wallet Address is MissMatch that you have enter.']);
            }
            // Step 1: Verify transaction receipt status
            $receiptStatusUrl = "$baseUrl/api?module=transaction&action=gettxreceiptstatus&txhash=$txHash&apikey=$bscApiKey";
            $receiptResponse = Http::get($receiptStatusUrl);
            if ($receiptResponse['status'] !== '1') {
                return response()->json(['status' => false, 'message' => 'Transaction hash not successful.']);
            }

            // Step 2: Fetch transaction details            
            $transactionDetailsUrl = "$baseUrl/api?module=account&action=tokentx&address=$adminWalletAddress&startblock=0&endblock=99999999&page=1&offset=50&sort=desc&apikey=$bscApiKey";
            $transactionResponse = Http::get($transactionDetailsUrl);

            if (!isset($transactionResponse['result'])) {
                return response()->json(['status' => false, 'message' => 'Transaction hash Failed to fetch details.']);
            }

            $amountInWei = bcmul($amount, bcpow('10', '18'));
            $transaction = is_array($transactionResponse['result']) ? $transactionResponse['result'] : json_decode($transactionResponse['result'], true);
            Log::error('Transaction Data get : ', ['transcation' => json_encode($transaction), 'amount_ewew' => $amountInWei]);
            //$tx['value'] == $amountInWei;
            // $matchedTransaction = collect($transaction)->first(function ($tx) use ($txHash, $amountInWei, $adminWalletAddress, $address, $tokenContractAddress) {
            //     return $tx['hash'] === $txHash &&
            //         strtolower($tx['to']) == strtolower($adminWalletAddress) &&
            //         strtolower($tx['from']) == strtolower($address) &&
            //         strtolower($tx['contractAddress']) === strtolower($tokenContractAddress);
            // });
            $matchedTransaction = [];
            foreach ($transaction as $tx) {
                if (
                    strtolower($tx['hash']) === strtolower($txHash) &&
                    strtolower($tx['to']) === strtolower($adminWalletAddress) &&
                    strtolower($tx['from']) === strtolower($address) &&
                    strtolower($tx['contractAddress']) === strtolower($tokenContractAddress)
                ) {
                    $matchedTransaction = $tx;
                    break; // Exit the loop once the match is found
                }
            }
            if (!$transaction) {
                return response()->json(['status' => false, 'message' => 'Transaction hash is not enter valid amount to address.']);
            }
            if (!$matchedTransaction) {
                return response()->json(['status' => false, 'message' => 'Transaction hash not found or invalid.']);
            }
            // Convert Wei to Token Base Unit
            $tokenAmount = bcdiv($matchedTransaction['value'], bcpow('10', '18'), 18); // Divide by 10^18 to get base unit

            // Log the transactions for debugging
            Log::error('Matched Transaction Data get : ', ['matchedTransaction' => $matchedTransaction, 'amount_ewew' => $amountInWei, 'tokenAmount' => $tokenAmount]);
            // Step 3: Process the transaction
            if ($tokenAmount >= $amount) {
                // Check for existing WalletAddress for the user
                $existingWallet = WalletAddress::where('userid', $user->id)->where('btc_address', $address)->first();

                if ($existingWallet) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Wallet address is already in use.',
                    ]);
                }

                // Create a new WalletAddress record
                WalletAddress::create([
                    'userid' => $user->id,
                    'btc_address' => $address,
                ]);
                $selected_wallet =  WalletType::where('wallet_column', 'like', 'c2')->where('wallet_type', 'like', 'wallet')->value('slug');
                // Call your backend service to mark the transaction as successful
                $tran = Transaction::create([
                    'u_code' => $user->id,
                    'tx_u_code' => $user->id,
                    'tx_type' => 'topup',
                    'debit_credit' => 'credit',
                    'wallet_type' => $selected_wallet,
                    'amount' => $amount,
                    'tx_charge' => $amount,
                    'date' => Carbon::now(),
                    'status' => 1,
                    'remark' => $user->username . " Register $user->name of amount $amount",
                ]);
                $user->auto_register = 1;
                $user->save();
                $userDetails = User::find($user->u_sponsor);
                $this->direct_destribute($userDetails, 0, $amount);
                if ($tran) {
                    return response()->json(['status' => true, 'message' => 'Transaction is verified and successful.']);
                }
            }

            return response()->json(['status' => false, 'message' => 'Transaction hash Failed verifying try again.']);
        } catch (\Exception $e) {
            Log::error('Transaction verification error:', ['error' => $e->getMessage()]);
            return response()->json(['status' => false, 'message' => 'Error verifying transaction. Please try again.'], 200);
        }
    }
}