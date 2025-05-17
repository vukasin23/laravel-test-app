<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Hall;
use App\Models\Phase;
class PhaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faze = ['Uithalen', 'Voorzieningen', 'Kozijnen', 'Vlechten', 'Storten'];

        foreach (Hall::all() as $hall) {
            foreach ($faze as $index => $faza) {
                Phase::create([
                    'hall_id' => $hall->id,
                    'name' => $faza,
                    'order' => $index + 1,
                    'aantal' => 20
                ]);
            }
        }
    }
}
