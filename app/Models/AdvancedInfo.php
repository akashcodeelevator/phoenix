<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdvancedInfo extends Model
{
    use HasFactory;
    protected $table = 'advanced_info';
    protected $fillable = [
        'title',
        'name',
        'label',
        'type',
        'options',
        'image',
        'value',
        'status',
        'admin_status',
    ];
}
