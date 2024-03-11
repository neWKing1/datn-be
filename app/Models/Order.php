<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'token',
        'status_id',
        'seller_by',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'recipient_city',
        'recipient_district',
        'recipient_ward',
        'recipient_detail',
        'recipient_note',
        'shipping_by',
        'shipping_cost',
        'order_discount',
        'payment_id',
        'payment_status',
        'is_process',
    ];

    public function variants(): HasManyThrough{
        return $this->hasManyThrough(Variant::class, OrderDetail::class,
        'order_id',
        'id',
        'id',
        'variant_id');
    }

    public function payment(): HasOne{
        return $this->hasOne(Payment::class,'id', 'payment_id');
    }

    public function status(): HasOne{
        return $this->hasOne(OrderStatus::class, 'id', 'status_id');
    }

    public function details(){
        return $this->hasMany(OrderDetail::class);
    }

    public function status_histories(){
        return $this->hasMany(OrderStatusHistory::class, 'order_id', 'id');
    }
}
