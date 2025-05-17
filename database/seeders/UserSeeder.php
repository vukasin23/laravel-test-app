<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $managerRole = Role::where('name', 'manager')->first();
        $voormanRole = Role::where('name', 'voorman')->first();

        User::create([
            'first_name' => 'Marko',
            'last_name' => 'Menadžer',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role_id' => $managerRole->id
        ]);

        User::create([
            'first_name' => 'Jovan',
            'last_name' => 'Voorman',
            'email' => 'jovan@example.com',
            'password' => Hash::make('password'),
            'role_id' => $voormanRole->id
        ]);

        User::create([
            'first_name' => 'Petar',
            'last_name' => 'Voorman',
            'email' => 'petar@example.com',
            'password' => Hash::make('password'),
            'role_id' => $voormanRole->id
        ]);
    }
}
