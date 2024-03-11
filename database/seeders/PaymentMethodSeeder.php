<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $methods = [
            [
                'id' => '1',
                'method' => 'Thanh toán khi nhận hàng'
            ],
            [
                'id' => '2',
                'method' => 'Thanh toán qua VNPay'
            ],
            [
                'id' => '3',
                'method' => 'Thanh toán bằng tiền mặt'
            ],
            [
                'id' => '4',
                'method' => 'Thanh toán trực tuyến'
            ]
        ];

        foreach ($methods as $method){
            Payment::create($method);
        }
    }
}
