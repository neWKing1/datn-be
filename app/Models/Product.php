<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'status',
        'is_active',
        'created_by',
        'updated_by',
        'updated_at'
    ];

    /**
     * @return HasMany
     */
    public function variants():hasMany
    {
        return $this->hasMany(Variant::class);
    }

    /**
     * @return BelongsToMany
     */
    public function sizes() : BelongsToMany
    {
        return $this->belongsToMany(Size::class, 'variants', 'product_id', 'size_id');
    }

    /**
     * @return BelongsToMany
     */
    public  function colors() : BelongsToMany
    {
        return $this->belongsToMany(Color::class, 'variants', 'product_id', 'color_id');
    }
}
