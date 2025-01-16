<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table = 'transaction';
    protected $fillable = [
        'payment_slip',
        'wallet_type',
        'tx_type',
        'payment_type',
        'cripto_type',
        'cripto_address',
        'debit_credit',
        'u_code',
        'source',
        'tx_u_code',
        'tx_charge',
        'payout_id',
        'tx_record',
        'amount',
        'date',
        'reason',
        'status',
        'remark',
        'bank_details',
    ];
}
