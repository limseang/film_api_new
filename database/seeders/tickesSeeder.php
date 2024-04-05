<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Ticket;

class tickesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i = 21; $i <= 120; $i++) {
            Ticket::create([
                'name' => 'Ticket ' . $i,
                'row' => 'A',
                'seat' => $i,
                //if less than 100, add 0 in front
                'code' => str_pad($i, 3, '0', STR_PAD_LEFT),
                'image' => '2620',
            ]);
        }

    }
}
