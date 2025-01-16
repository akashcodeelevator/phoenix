<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletAddress extends Model
{
    use HasFactory;
    protected $table = 'wallet_address';
    protected $fillable = [
        'btc_address',
        'added',
        'userid',
        'xpub',
    ];
}