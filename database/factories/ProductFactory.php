<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product_name = $this->faker->unique()->name();
        $slug = Str::slug($product_name, '-');
        return [
            //
            'name' => $product_name,
            'slug' => $slug,
            'status' => fake()->boolean(),
            'created_by' => fake()->name,
            'updated_by' => fake()->name,
        ];
    }
}
