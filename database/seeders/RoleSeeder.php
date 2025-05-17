<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['name' => 'manager'],
            ['label' => 'Menadžer']
        );

        DB::table('roles')->updateOrInsert(
            ['name' => 'voorman'],
            ['label' => 'Vođa hale']
        );
        DB::table('roles')->insertOrIgnore([
            'name'  => 'werkvoorbereider',
            'label' => 'Werkvoorbereider',
        ]);
    }
}
