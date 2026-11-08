<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])
            || ! in_array(DB::connection('shared_users')->getDatabaseName(), ['testing', 'twitter_clone_assignment'], true)) {
            throw new \RuntimeException('Synthetic Twitter posts may only be seeded locally.');
        }
        $user = User::where('email', 'twitter-demo@example.test')->firstOrFail();
        Post::firstOrCreate(['user_id' => $user->id, 'content' => 'A synthetic post for the Twitter working demonstration.']);
    }
}
