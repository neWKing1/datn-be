<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    protected $fillable = [
        'order_id',
        'variant_id',
        'name',
        'unit_price',
        'quantity'
    ];

    public function variant(): HasOne {
        return $this->hasOne(Variant::class, 'id', 'variant_id');
    }

    public function promotion(): HasOneThrough {
        return $this->hasOneThrough(
            Promotion::class,
            OrderPromotion::class,
        'order_detail_id',
        'id',
        'id',
        'promotion_id');
    }

    public function order_promotion() :HasOne {
        return $this->hasOne(OrderPromotion::class, 'order_detail_id', 'id');
    }
}
