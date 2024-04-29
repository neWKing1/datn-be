<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('id', '=', '1')->first();
        $products = [
            [
                'id' => 1,
                'name' => 'Giày Đi Bộ Đường Dài Nam Columbia Facet™ 75 Outdry™',
                'slug' => Str::slug('Giày Đi Bộ Đường Dài Nam Columbia Facet™ 75 Outdry™'),
                'created_by' => $owner->name ?? 'Hệ thống',
                'updated_by' => $owner->name ?? 'Hệ thống',
                'status' => 0,
                'is_active' => 0,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
