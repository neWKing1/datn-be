<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'price',
        'quantity',
        'bill_id',
        'variant_id',
    ];
    public  function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
