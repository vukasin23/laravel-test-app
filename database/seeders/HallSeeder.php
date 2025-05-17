<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Hall;
class HallSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jovan = User::where('email', 'jovan@example.com')->first();
        $petar = User::where('email', 'petar@example.com')->first();

        Hall::create(['name' => 'HAL-1', 'voorman_id' => $jovan->id, 'status' => 'active']);
        Hall::create(['name' => 'HAL-2', 'voorman_id' => $petar->id, 'status' => 'active']);
    }
}
