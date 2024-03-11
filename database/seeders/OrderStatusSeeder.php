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
              'id' => 2,
              'status' => 'Đang xử lý'
            ],
            [
                'id'=> 3,
                'status' => 'Chờ xác nhận'
            ],
            [
                'id'=> 4,
                'status' => 'Đã xác nhận'
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
            [
                'id'=> 9,
                'status' => 'Đã hủy'
            ],
            [
                'id' => 10,
                'status' => 'Đặt hàng thất bại'
            ],
            [
                'id' => 11,
                'status' => 'Đặt hàng thành công'
            ],
        ];

        foreach ($data as $status){
            OrderStatus::create($status);
        }
    }
}
