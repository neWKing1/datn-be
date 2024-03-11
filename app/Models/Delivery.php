<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $table = 'deliveries';

    protected $fillable = [
        'user_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'recipient_city',
        'recipient_city_name',
        'recipient_district',
        'recipient_district_name',
        'recipient_ward',
        'recipient_ward_name',
        'recipient_detail',
    ];
}
