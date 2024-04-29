<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $colors = [
            [
                'id' => 1,
                'name' => 'Xanh Dương'
            ],
            [
                'id' => 2,
                'name' => 'Đỏ'
            ],
            [
                'id' => 3,
                'name' => 'Trắng'
            ],
            [
                'id' => 4,
                'name' => 'Đen'
            ],
            [
                'id' => 5,
                'name' => 'Xanh Lá'
            ],
        ];

        foreach ($colors as $color) {
            Color::create($color);
        }
    }
}
