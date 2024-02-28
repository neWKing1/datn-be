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
            [
                'id'=> 1,
                'status' => 'Chờ thanh toán'
            ],
            [
                'id'=> 2,
                'status' => 'Chờ xác nhận'
            ],
            [
                'id'=> 3,
                'status' => 'Đã xác nhận'
            ],
            [
                'id'=> 4,
                'status' => 'Chuẩn bị gửi hàng'
            ],
            [
                'id'=> 5,
                'status' => 'Đang giao hàng'
            ],
            [
                'id'=> 6,
                'status' => 'Giao hàng thành công'
            ],
            [
                'id'=> 7,
                'status' => 'Giao hàng thất bại'
            ],
            [
                'id'=> 8,
                'status' => 'Trả hàng'
            ],
        ];

        foreach ($data as $status){
            OrderStatus::create($status);
        }
    }
}
