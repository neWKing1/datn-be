<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'order_status_histories';

    protected $fillable = [
        'order_id',
        'order_status_id',
        'note'
    ];

    public function status(): HasOne{
        return $this->hasOne(OrderStatus::class, 'id', 'order_status_id');
    }
}
