<?php

namespace Database\Seeders;

use App\Models\Designation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $designations = [
            ['name' => 'QA', 'description' => 'Quality Assurance'],
            ['name' => 'IT', 'description' => 'Information Technology'],
        ];

        foreach ($designations as $data)
        {
            Designation::firstOrCreate(['name' => $data['name']], $data);
        }
    }
}
