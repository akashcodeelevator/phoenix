<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinHistory extends Model
{
    use HasFactory;

    protected $table = 'pin_history';
    protected $fillable = [
        'user_id',
        'debit',
        'prev_pin',
        'curr_pin',
        'pin_type',
        'tx_type',
        'remark',
    ];
}
