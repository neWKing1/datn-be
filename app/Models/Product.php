<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'quantity',
        'status',
    ];

    /**
     * @return BelongsToMany
     */
    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(Color::class);
    }
    /**
     * @return BelongsToMany
     */
    public function sizes(): BelongsToMany
    {
        return $this->belongsToMany(Size::class);
    }
    /**
     * @return BelongsToMany
     */
    public function meterials(): BelongsToMany
    {
        return $this->belongsToMany(Meterial::class);
    }
}
