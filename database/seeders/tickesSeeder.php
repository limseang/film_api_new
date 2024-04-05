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
                'code' => '0' . $i,
                'image' => '2620',
            ]);
        }

    }
}
