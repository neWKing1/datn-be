<?php

namespace Database\Seeders;

use App\Models\ImageGallery;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GalleryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $galleries = [
            [
                'id' => 1,
                'folder' => 'XanhDuong',
                'url' => 'product_1.png',
            ],
            [
                'id' => 2,
                'folder' => 'Den',
                'url' => 'product_1_den.png',
            ],
            [
                'id' => 3,
                'folder' => 'XanhLa',
                'url' => 'product_1_xanhla.png',
            ],
        ];

        foreach ($galleries as $gallery) {
            ImageGallery::create($gallery);
        }
    }
}
