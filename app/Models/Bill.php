<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'status',
        'customer_name',
        'note',
        'total_money',
        'money_reduce',
        'address',
        'money_ship',
        'timeline',
        'type',
        'payment_method',
        'voucher_id',
        'customer_id',
        'phone_number',
        'email',
        'status_id',
        'payment_id',
        'address_information'
    ];
    public  function billDetails()
    {
        return $this->hasMany(BillDetail::class);
    }
    public  function variants()
    {
        return $this->belongsToMany(Variant::class, 'bill_details', 'bill_id', 'variant_id');
    }

    public function payment(){
        return $this->belongsTo(Payment::class, 'payment_id', 'id');
    }

    public function status(){
        return $this->belongsTo(BillStatus::class, 'status_id', 'id');
    }

    public function status_histories(){
        return $this->hasMany(BillHistory::class, 'bill_id', 'id');
    }
}
