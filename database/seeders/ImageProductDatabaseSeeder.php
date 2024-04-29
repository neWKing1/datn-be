<?php

namespace Database\Seeders;

use App\Models\ImageProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImageProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $images = [
            [
                'id' => 1,
                'variant_id' => 1,
                'image_gallery_id' => 1,
            ],
            [
                'id' => 2,
                'variant_id' => 2,
                'image_gallery_id' => 1,
            ],
            [
                'id' => 3,
                'variant_id' => 3,
                'image_gallery_id' => 1,
            ],
            [
                'id' => 4,
                'variant_id' => 4,
                'image_gallery_id' => 2,
            ],
            [
                'id' => 5,
                'variant_id' => 5,
                'image_gallery_id' => 2,
            ],
            [
                'id' => 6,
                'variant_id' => 6,
                'image_gallery_id' => 3,
            ],
            [
                'id' => 7,
                'variant_id' => 7,
                'image_gallery_id' => 3,
            ],
            [
                'id' => 8,
                'variant_id' => 8,
                'image_gallery_id' => 3,
            ],
        ];

        foreach ($images as $image) {
            ImageProduct::create($image);
        }
    }
}
