<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $recordsCount = (int)$this->command->ask('How many records would you like?', 20);

        $users = \App\Models\User::factory($recordsCount)->create();
        $this->call(OrderStatusSeeder::class);
        $this->call(PaymentMethodSeeder::class);
//        $colors = \App\Models\Color::factory($recordsCount)->create();
//        $sizes = \App\Models\Size::factory($recordsCount)->create();
//        $products = \App\Models\Product::factory($recordsCount)->create();
//
//        $variants = \App\Models\Variant::factory($recordsCount)->make()->each(function ($variant) use ($colors, $sizes, $products) {
//            $faker = \Faker\Factory::create();
//            $variant->quantity = $faker->randomDigitNotNull();
//            $variant->price = $faker->randomDigitNotNull();
//            $variant->weight = $faker->randomDigitNotNull();
//            $variant->color_id = $colors->random()->id;
//            $variant->size_id = $sizes->random()->id;
//            $variant->product_id = $products->random()->id;
//            $variant->save();
//        });

        $this->command->info('Successfully seeded.');
    }
}
