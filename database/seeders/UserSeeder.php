<?php

namespace Database\Seeders;

use Hash;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate([
            "name" => "Kristofer Pillarina",
            "email" => "admin@localhost.test",
            "password"=> Hash::make("password"),
            "role" => "administrator",
        ]);

        User::firstOrCreate([
            "name" => "Juan Dela Cruz",
            "email" => "user@localhost.test",
            "password"=> Hash::make("password"),
            "role" => "user",
        ]);

        // User::factory()->count(500)->create();
    }
}
