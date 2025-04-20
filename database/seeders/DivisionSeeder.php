<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            ['name' => 'SMD', 'description' => 'Strategic Management Division'],
            ['name' => 'DMD', 'description' => 'Development Management Division'],
            ['name' => 'ITPMD', 'description' => 'IT Project Management Division'],
            ['name' => 'ITSD', 'description' => 'IT Support Division'],
        ];

        foreach ($divisions as $data)
        {
            Division::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
