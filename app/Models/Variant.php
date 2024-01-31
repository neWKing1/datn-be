<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

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

    public function imageProducts(): hasMany
    {
        return $this->hasMany(ImageProduct::class);
    }

    public function images(): HasManyThrough
    {
        return $this->hasManyThrough(ImageGallery::class,
            ImageProduct::class,
            'variant_id',
            'id',
            'id',
            'image_gallery_id'
            );
    }
}
