<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_details',
        'u_code',
        'tx_user_id',
        'tx_type',
        'order_amount',
        'order_bv',
        'pv',
        'roi',
        'status',
        'payout_id',
        'payout_status',
        'package_type',
        'active_id',
        'topup_from_user_id',
        'country_name',
    ];
    public function topupFromUser()
    {
        return $this->belongsTo(User::class, 'tx_user_id');
    }
}