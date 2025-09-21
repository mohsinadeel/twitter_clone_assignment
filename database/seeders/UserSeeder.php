<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a test user
        User::factory()->create([
            'name' => 'Mohsin Adeel',
            'email' => 'mohsinadeel@gmail.com',
            'username' => 'mohsinadeel',
            'password' => bcrypt('Mohsin@123'),
        ]);

        // Create additional users
        User::factory(5)->create();
    }
}
