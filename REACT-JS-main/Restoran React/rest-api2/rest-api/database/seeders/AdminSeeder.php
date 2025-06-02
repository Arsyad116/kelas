<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
            'role' => 'admin',
            'status' => 'aktif',
            'api_token' => Str::random(60),
        ]);

        $this->command->info('Admin user created successfully!');
    }
}
