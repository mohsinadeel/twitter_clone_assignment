<?php

namespace Database\Seeders;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class LocalSharedUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])
            || ! in_array(DB::connection('shared_users')->getDatabaseName(), ['testing', 'twitter_clone_assignment'], true)) {
            throw new RuntimeException('The shared-users fixture is restricted to the disposable local Twitter database or testing database.');
        }
        $schema = Schema::connection('shared_users');
        if (! $schema->hasTable('users')) {
            $schema->create('users', function (Blueprint $table): void {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('username')->nullable()->unique();
                $table->string('avatar', 500)->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->text('two_factor_secret')->nullable();
                $table->text('two_factor_recovery_codes')->nullable();
                $table->timestamp('two_factor_confirmed_at')->nullable();
                $table->boolean('is_admin')->default(false);
                $table->string('stripe_id')->nullable();
                $table->string('pm_type')->nullable();
                $table->string('pm_last_four', 4)->nullable();
                $table->timestamp('trial_ends_at')->nullable();
                $table->timestamps();
            });
        }

        foreach (['username', 'avatar', 'two_factor_secret'] as $column) {
            if (! $schema->hasColumn('users', $column)) {
                throw new RuntimeException('The existing user schema is incomplete; run the main-owned migrations first.');
            }
        }
    }
}
