<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'id' => '100',
                'method' => 'Thanh toán khi nhận hàng'
            ],
            [
                'id' => '101',
                'method' => 'Thanh toán qua VNPay'
            ],
        ];

        foreach ($methods as $method){
            Payment::create($method);
        }
    }
}
