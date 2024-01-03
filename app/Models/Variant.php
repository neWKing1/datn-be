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
        'size_id',
        'color_id',
        'meterial_id',
        'product_id'
    ];
}
