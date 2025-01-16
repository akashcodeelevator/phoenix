<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'u_code',
        'kyc_status',
        'kyc_remark',
        'document_type',
        'front_image_pan',
        'pan_no',
        'pan_image',
        'pan_kyc_status',
        'pan_kyc_date',
        'pan_remark',
        'adhaar_no',
        'adhaar_image',
        'adhaar_back_image',
        'adhaar_kyc_date',
        'adhaar_kyc_status',
        'adhaar_remark',
        'account_type'
    ];
}
