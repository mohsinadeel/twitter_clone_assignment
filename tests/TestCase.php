<?php

namespace Tests;

use Database\Seeders\LocalSharedUsersSeeder;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    private static bool $schemaPrepared = false;

    public function createApplication(): Application
    {
        $app = parent::createApplication();
        $config = $app->make('config');
        foreach (['mysql', 'shared_users'] as $connection) {
            if (! $app->environment('testing') || $config->get('database.default') !== 'mysql'
                || $config->get("database.connections.$connection.database") !== 'testing'
                || $config->get("database.connections.$connection.url")) {
                throw new RuntimeException('Twitter tests require both connections on the dedicated MySQL testing database.');
            }
        }
        if ($config->get('database.connections.mysql.prefix') !== 'tw_test_'
            || $config->get('database.connections.shared_users.prefix') !== '') {
            throw new RuntimeException('Twitter tests require tw_test_ owned tables and unprefixed shared users.');
        }
        if (! self::$schemaPrepared) {
            $app->make(LocalSharedUsersSeeder::class)->run();
            if ($app->make(Kernel::class)->call('migrate', ['--force' => true]) !== 0) {
                throw new RuntimeException('Twitter test migrations did not complete.');
            }
            self::$schemaPrepared = true;
        }
        // One transaction must cover both grammars so foreign keys see uncommitted factory users.
        $pdo = DB::connection('mysql')->getPdo();
        DB::connection('shared_users')->setPdo($pdo)->setReadPdo($pdo);

        return $app;
    }
}
