<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedInfo;
use App\Models\Order;
use App\Models\PinDetail;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserWallet;
use App\Models\WalletType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $currentRoi = PinDetail::where('status', 1)->value('roi');
        return view('admin.settings.index', compact('currentRoi'));
    }
    public function setting_update(Request $request)
    {
        // Validate the input
        $request->validate([
            'roi' => 'required|min:0',
        ]);

        // Update the ROI value in the database
        PinDetail::where('status', 1)->where('id', 1)->update(['roi' => $request->roi]);
        $this->roi();
        return redirect()->route('admin.settings.index')->with('success', 'ROI updated successfully.');
    }
    public function roi(){
        $source="roi";
        $admin_per= PinDetail::where('status',1)->value('roi');
        $currentDate = Carbon::now()->toDateString();
        
        $all_order =  $order = Order::where('status',1)->get();
       
        $reg_type = AdvancedInfo::where('label', 'like', 'reg_type')->value('value');
         
        foreach($all_order as $order){
            $userid=$order->u_code;
            $roi_incomes=$order->order_amount*$admin_per/100;
            $info_u=User::where('id',$userid)->first();
            $name = $info_u->name;
            $username = $info_u->username;
            $userWallet = UserWallet::firstOrNew(['u_code' => $userid]);
            // Parse and handle null
            $lastCronRun = $userWallet->last_cron_run ? Carbon::parse($userWallet->last_cron_run)->toDateString() : null;
            
            if ($lastCronRun === null || $lastCronRun !== $currentDate) {
                $transaction = [
                    'u_code' => $userid,
                    'tx_type' => 'income',
                    'source' => $source,
                    'debit_credit' => 'credit',
                    'amount' => $roi_incomes,
                    'tx_charge' => 0,
                    'date' =>Carbon::today(),
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

    public function withdrawal_index (Request $request)
    {
        $settings = AdvancedInfo::where('title','Withdrawal')->orwhereIn('label',['user_gen_prefix','user_gen_digit','user_gen_prefix'])->get(); // Fetch all advanced settings
        return view('admin.settings.withdrawal_index', compact('settings'));
    }
    public function withdrawal_setting_update(Request $request)
    {
        // Validate the input
        $settings = AdvancedInfo::all();

        foreach ($settings as $setting) {
            if ($request->has($setting->label)) {
                $setting->update(['value' => $request->input($setting->label)]);
            }
        }

        return redirect()->route('admin.withdrawal_settings.index')->with('success', 'Setttings updated successfully.');
    }
}
