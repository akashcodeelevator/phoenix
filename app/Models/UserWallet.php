<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'u_code',   
        'c1', 
        'c2',    
        'c3',
        'c5',     
        'c8',    
        'c9',
        'c10',     
        'c11',
        'c17',    
        'c19',      
        'c20',
        'c21',
        'last_cron_run',
    ];
}
