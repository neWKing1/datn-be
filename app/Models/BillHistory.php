<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillHistory extends Model
{
    use HasFactory;
    protected $fillable = [
        'note',
        'bill_id',
        'status_id',
        'created_by',
        'status'
    ];

    public function status(){
        return $this->belongsTo(BillStatus::class, 'status_id', 'id');
    }
}
