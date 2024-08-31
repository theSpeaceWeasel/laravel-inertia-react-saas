<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Package;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' =>  bcrypt('password'),
        ]);

        Feature::factory()->create([
            'image' => 'https://cdn-icons-png.flaticon.com/512/8922/8922789.png',
            'name' => 'Calculate Sum',
            'route_name' =>  'feature1.index',
            'description' =>  'calcumate the sum of 2 numbers',
            'required_credits' =>  1,
            'active' => true
        ]);

        Feature::factory()->create([
            'image' => 'https://cdn-icons-png.flaticon.com/512/2569/2569198.png',
            'name' => 'Calculate Difference',
            'route_name' =>  'feature2.index',
            'description' =>  'calcumate the diff of 2 numbers',
            'required_credits' =>  3,
            'active' => true
        ]);

        Package::factory()->create([
            'name' => 'Basic',
            'price' => 5,
            'credits' =>  20,
        ]);
        Package::factory()->create([
            'name' => 'Silver',
            'price' => 20,
            'credits' =>  100,
        ]);
        Package::factory()->create([
            'name' => 'Gold',
            'price' => 50,
            'credits' =>  500,
        ]);
    }
}