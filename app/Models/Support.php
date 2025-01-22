<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    use HasFactory;
    protected $table = 'support'; // The name of your database table

    protected $fillable = [
        'message',
        'first_name',
        'u_code',
        'email',
        'contactno',
        'ticket',
        'status',
        'reply_status',
        'reply',
    ];
}