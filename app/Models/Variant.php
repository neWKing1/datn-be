<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    public function size()
    {
        return $this->belongsTo(Size::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public  function imageProducts() : hasMany
    {
        return $this->hasMany(ImageProduct::class);
    }
    public  function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_variants', 'variant_id', 'promotion_id');
    }
}
