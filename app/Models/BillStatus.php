<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillStatus extends Model
{
    use HasFactory;
    protected $table = 'bill_status';

    protected $fillable = ['status'];

    public function status_histories(){
        return $this->hasMany(BillHistory::class, 'status_id', 'id');
    }
}
