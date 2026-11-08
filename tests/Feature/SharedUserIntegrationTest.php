<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SharedUserIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_shared_credentials_issue_only_demo_owned_tokens_and_keep_private_fields_hidden(): void
    {
        $user = User::factory()->create(['password' => 'example-password']);
        $user->forceFill(['stripe_id' => 'cus_private', 'is_admin' => true])->save();
        $response = $this->postJson('/api/login', ['email' => $user->email, 'password' => 'example-password']);
        $response->assertOk()->assertJsonPath('data.user.id', $user->id);
        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
        $this->assertFalse(Schema::connection('shared_users')->hasTable('personal_access_tokens'));
        $this->assertArrayNotHasKey('stripe_id', $response->json('data.user'));
        $this->assertArrayNotHasKey('is_admin', $response->json('data.user'));
        $this->assertArrayNotHasKey('password', $response->json('data.user'));
    }

    public function test_two_factor_accounts_cannot_bypass_the_portal_with_password_only_login_or_old_tokens(): void
    {
        $user = User::factory()->create(['password' => 'example-password']);
        $token = $user->createToken('before-two-factor')->plainTextToken;
        $user->forceFill(['two_factor_secret' => 'encrypted-by-main', 'two_factor_confirmed_at' => now()])->save();
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'example-password'])->assertForbidden();
        $this->withToken($token)->getJson('/api/user')->assertForbidden();
        $this->flushHeaders();
        $this->app['auth']->forgetGuards();
        $this->app['auth']->shouldUse('web');
        $this->post('/login', ['email' => $user->email, 'password' => 'example-password'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_registration_and_validation_use_shared_users(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Synthetic Demo', 'email' => 'new-user@example.test', 'username' => 'synthetic_demo',
            'password' => 'example-password', 'password_confirmation' => 'example-password',
        ]);
        $response->assertCreated();
        $this->assertDatabaseHas('users', ['email' => 'new-user@example.test'], 'shared_users');
        $this->postJson('/api/register', [
            'name' => 'Another Demo', 'email' => 'new-user@example.test', 'username' => 'synthetic_demo',
            'password' => 'example-password', 'password_confirmation' => 'example-password',
        ])->assertUnprocessable();
        $this->assertFalse(Schema::hasTable('users'));
    }

    public function test_prefixed_posts_reference_shared_users_and_preserve_the_owner_boundary(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        $this->actingAs($other)->deleteJson('/api/v1/posts/'.$post->id)->assertForbidden();
        $this->assertModelExists($post);
        $this->assertSame($owner->id, $post->user->id);
        $this->assertSame($post->id, $owner->posts()->first()->id);
    }

    public function test_nested_home_page_keeps_api_and_asset_urls_inside_the_demo(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->withServerVariables([
            'SCRIPT_NAME' => '/twitter-clone-demo/index.php',
            'PHP_SELF' => '/twitter-clone-demo/index.php',
            'SCRIPT_FILENAME' => public_path('index.php'),
        ])->get('http://localhost/twitter-clone-demo/home')
            ->assertOk()->assertSee('/twitter-clone-demo/api/v1', false)
            ->assertSee('/twitter-clone-demo/build/assets/', false);
    }
}
