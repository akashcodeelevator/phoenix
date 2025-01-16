<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
    protected $fillable = [
        'name',
        'email',
        'password',
        'u_sponsor', 'parentid', 'position', 'matrix_pool', 'matrix_position', 'username',
        'eth_address', 'user_type', 'title', 'mobile', 'tx_password',
        'set_user_password_status', 'is_fan', 'address', 'address2', 'father_name', 'city', 'gender',
        'dob', 'email_code', 'is_email_verify', 'post_code', 'country', 'country_code', 'marital_status',
        'pan_no', 'direct', 'img', 'state', 'admin_register_status', 'active_id', 'active_status',
        'trade_status', 'rank_id', 'retopup_status', 'fast_achiever_rank', 'token_status', 'retopup_date',
        'installment_date', 'block_status', 'pass_status', 'tx_pass_status', 'profile_edited', 'active_date',
        'sip_status', 'sip_date', 'my_package', 'my_rank', 'salary_rank', 'income_per', 'growth_rank',
        'rank_bonus', 'pkg_type', 'income_status', 'booster_status', 'booster1_status', 'booster2_status',
        'booster3_status', 'booster1_date', 'booster2_date', 'booster3_date', 'auto_register', 'notifications',
        'updated_on', 'added_on', 'nominee_name', 'nominee_relation', 'nominee_dob', 'instagram_link',
        'facebook_link', 'twitter_link', 'telegram_link', 'snap_chat', 'roi_income', 'booster_income',
        'level_roi_income'
    ];

    /**
    * The attributes that should be hidden for serialization.
    *
    * @var array<int, string>
    */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
    * The attributes that should be cast.
    *
    * @var array<string, string>
    */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function ReferralUser()
    {
        return $this->belongsTo(User::class, 'u_sponsor');
    }
    public function WalletUsers()
    {
        return $this->hasOne(UserWallet::class, 'u_code', 'id');
    }
}
