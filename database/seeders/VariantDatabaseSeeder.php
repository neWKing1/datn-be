<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VariantDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $variants = [
            [
                'id' => 1,
                'quantity' => 102,
                'weight' => 1500,
                'price' => 3990000,
                'size_id' => 1,
                'color_id' => 1,
                'product_id' => 1
            ],
            [
                'id' => 2,
                'quantity' => 16,
                'weight' => 1500,
                'price' => 3990000,
                'size_id' => 2,
                'color_id' => 1,
                'product_id' => 1
            ],
            [
                'id' => 3,
                'quantity' => 80,
                'weight' => 1500,
                'price' => 3990000,
                'size_id' => 3,
                'color_id' => 1,
                'product_id' => 1
            ],
            [
                'id' => 4,
                'quantity' => 22,
                'weight' => 1500,
                'price' => 1990000,
                'size_id' => 1,
                'color_id' => 4,
                'product_id' => 1
            ],
            [
                'id' => 5,
                'quantity' => 10,
                'weight' => 1500,
                'price' => 1990000,
                'size_id' => 3,
                'color_id' => 4,
                'product_id' => 1
            ],
            [
                'id' => 6,
                'quantity' => 16,
                'weight' => 1500,
                'price' => 2990000,
                'size_id' => 1,
                'color_id' => 5,
                'product_id' => 1
            ],
            [
                'id' => 7,
                'quantity' => 19,
                'weight' => 1500,
                'price' => 2590000,
                'size_id' => 2,
                'color_id' => 5,
                'product_id' => 1
            ],
            [
                'id' => 8,
                'quantity' => 28,
                'weight' => 1500,
                'price' => 2290000,
                'size_id' => 3,
                'color_id' => 5,
                'product_id' => 1
            ],
        ];

        foreach ($variants as $variant) {
            $variant = Variant::create($variant);
            $product = $variant->product;
            $product->status = 1;
            $product->is_active = 1;
            $product->save();
        }
    }
}
