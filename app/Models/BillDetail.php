<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class BillDetail extends Model
{
    use HasFactory;
    protected $fillable = [
        'price',
        'quantity',
        'bill_id',
        'variant_id',
    ];

    public function variant(){
        return $this->belongsTo(Variant::class, 'variant_id', 'id');
    }

    public function promotion(): HasOneThrough {
        return $this->hasOneThrough(
            Promotion::class,
            BillPromotion::class,
            'bill_detail_id',
            'id',
            'id',
            'promotion_id');
    }

}
