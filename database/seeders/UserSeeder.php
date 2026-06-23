<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'username'   => 'admin',
                'password'   => Hash::make('admin123'),
                'name'       => 'Administrator',
                'role'       => 'admin',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'inspector',
                'password'   => Hash::make('inspector123'),
                'name'       => 'Inspector User',
                'role'       => 'inspector',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'username'   => 'supervisor',
                'password'   => Hash::make('supervisor123'),
                'name'       => 'Supervisor User',
                'role'       => 'supervisor',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
