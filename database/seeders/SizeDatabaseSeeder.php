<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = [
            [
                'id' => 1,
                'name' => '38'
            ],
            [
                'id' => 2,
                'name' => '39'
            ],
            [
                'id' => 3,
                'name' => '40'
            ],
            [
                'id' => 4,
                'name' => '41'
            ],
        ];

        foreach ($sizes as $size) {
            Size::create($size);
        }
    }
}
