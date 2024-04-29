<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'name' => "Owner",
                'role' => 'owner',
                'status' => 'active',
                'email' => 'owner@fshoes.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fshoes123'),
                'remember_token' => Str::random(10),
                'phone_number' => '0988765432',
                'province' => fake('vi_VN')->city(),
                'district' => fake('vi_VN')->streetAddress(),
                'ward' => fake('vi_VN')->buildingNumber(),
                'specific_address'=> fake('vi_VN')->address(),
            ],
            [
                'id' => 2,
                'name' => "Staff",
                'role' => 'staff',
                'status' => 'active',
                'email' => 'staff@fshoes.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fshoes123'),
                'remember_token' => Str::random(10),
                'phone_number' => '0912345678',
                'province' => fake('vi_VN')->city(),
                'district' => fake('vi_VN')->streetAddress(),
                'ward' => fake('vi_VN')->buildingNumber(),
                'specific_address'=> fake('vi_VN')->address(),
            ],
            [
                'id' => 3,
                'name' => "Customer",
                'role' => 'customer',
                'status' => 'active',
                'email' => 'customer@fshoes.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fshoes123'),
                'remember_token' => Str::random(10),
                'phone_number' => '0984632765',
                'province' => fake('vi_VN')->city(),
                'district' => fake('vi_VN')->streetAddress(),
                'ward' => fake('vi_VN')->buildingNumber(),
                'specific_address'=> fake('vi_VN')->address(),
            ],
            [
                'id' => 4,
                'name' => "Quoc Pham",
                'role' => 'customer',
                'status' => 'active',
                'email' => 'quocpham@fshoes.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fshoes123'),
                'remember_token' => Str::random(10),
                'phone_number' => '0984632761',
                'province' => fake('vi_VN')->city(),
                'district' => fake('vi_VN')->streetAddress(),
                'ward' => fake('vi_VN')->buildingNumber(),
                'specific_address'=> fake('vi_VN')->address(),
            ],
            [
                'id' => 5,
                'name' => "Thang Nguyen",
                'role' => 'customer',
                'status' => 'inactive',
                'email' => 'thangnguyen@fshoes.com',
                'email_verified_at' => now(),
                'password' => Hash::make('fshoes123'),
                'remember_token' => Str::random(10),
                'phone_number' => '0387561289',
                'province' => fake('vi_VN')->city(),
                'district' => fake('vi_VN')->streetAddress(),
                'ward' => fake('vi_VN')->buildingNumber(),
                'specific_address'=> fake('vi_VN')->address(),
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
