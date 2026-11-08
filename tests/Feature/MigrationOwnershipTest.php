<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LocalSharedUsersSeeder;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Tests\TestCase;

class MigrationOwnershipTest extends TestCase
{
    public function test_demo_rollback_preserves_shared_users_and_their_profile_fields(): void
    {
        $user = User::factory()->create(['username' => 'ownership_probe']);
        try {
            $this->artisan('migrate:rollback', ['--force' => true])->assertSuccessful();
            $this->assertTrue(Schema::connection('shared_users')->hasTable('users'));
            $this->assertFalse(Schema::hasTable('posts'));
            $this->assertFalse(Schema::hasTable('users'));
            $this->assertDatabaseHas('users', ['id' => $user->id, 'username' => 'ownership_probe'], 'shared_users');
        } finally {
            $this->artisan('migrate', ['--force' => true])->assertSuccessful();
            $user->delete();
        }
    }

    public function test_local_user_fixture_is_refused_in_production(): void
    {
        $this->app->instance('env', 'production');
        $this->expectException(RuntimeException::class);
        $this->app->make(LocalSharedUsersSeeder::class)->run();
    }
}
