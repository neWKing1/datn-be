<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillPromotion extends Model
{
    use HasFactory;
    protected $table = 'bill_promotion';

    protected $fillable = [
        'bill_detail_id',
        'promotion_id'
    ];
}
