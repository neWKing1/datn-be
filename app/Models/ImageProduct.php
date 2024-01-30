<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'variant_id',
        'image_gallery_id'
    ];
    public function imageGallery(): BelongsTo
    {
        return $this->belongsTo(ImageGallery::class);
    }
}
