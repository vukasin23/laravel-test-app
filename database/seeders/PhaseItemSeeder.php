<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Phase;
use App\Models\PhaseItem;

class PhaseItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phases = Phase::all();

        foreach ($phases as $phase) {
            for ($i = 1; $i <= $phase->aantal; $i++) {
                PhaseItem::create([
                    'phase_id' => $phase->id,
                    'number' => $i,
                    'is_done' => fake()->boolean(50), // random true/false da testiramo procente
                ]);
            }
        }
    }
}
