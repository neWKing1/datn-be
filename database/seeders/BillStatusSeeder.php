<?php

namespace Database\Seeders;

use App\Models\BillStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BillStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 100,
                'status' => 'Chờ thanh toán'
            ],
            [
                'id' => 101,
                'status' => 'Đang xử lý'
            ],
            [
                'id' => 102,
                'status' => 'Chờ xác nhận'
            ],
            [
                'id' => 103,
                'status' => 'Đã xác nhận'
            ],
            [
                'id' => 104,
                'status' => 'Đang giao hàng'
            ],
            [
                'id' => 105,
                'status' => 'Giao hàng thành công'
            ]
            ,
            [
                'id' => 106,
                'status' => 'Giao hàng thất bại'
            ],
            [
                'id' => 107,
                'status' => 'Trả hàng'
            ],
            [
                'id' => 108,
                'status' => 'Đã hủy'
            ],
            [
                'id' => 109,
                'status' => 'Đặt hàng thất bại'
            ]
        ];

        foreach ($data as $status) {
            BillStatus::create($status);
        }
    }
}
