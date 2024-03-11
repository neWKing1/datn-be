<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPromotion extends Model
{
    use HasFactory;

    protected $table = 'order_promotions';

    protected $fillable = [
        'order_detail_id',
        'promotion_id',
    ];
}
