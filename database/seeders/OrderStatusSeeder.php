<?php

namespace Database\Seeders;

use App\Models\OrderStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Đang xử lý',
            'Đã xác nhận',
            'Chuẩn bị gửi hàng',
            'Đang giao hàng',
            'Đã giao hàng',
            'Giao hàng thất bại',
            'Trả hàng'
        ];

        foreach ($data as $status){
            OrderStatus::create([
                'status' => $status
            ]);
        }
    }
}
