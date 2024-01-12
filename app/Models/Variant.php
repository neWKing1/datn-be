<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'price',
        'weight',
        'size_id',
        'color_id',
        'product_id'
    ];
}
