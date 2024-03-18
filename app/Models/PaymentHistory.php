<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_money',
        'trading_code',
        'method',
        'note',
        'created_by',
        'bill_id'
    ];
}
