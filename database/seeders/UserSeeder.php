<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])
            || ! in_array(DB::connection('shared_users')->getDatabaseName(), ['testing', 'twitter_clone_assignment'], true)) {
            throw new \RuntimeException('Synthetic Twitter accounts may only be seeded locally.');
        }
        User::firstOrCreate(['email' => 'twitter-demo@example.test'], [
            'name' => 'Twitter Demo', 'username' => 'twitter_demo', 'password' => 'password',
        ]);
    }
}
