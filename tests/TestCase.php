<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        /** @var Application $app */
        $app = require __DIR__ . '/../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        $this->configureTestEnvironment($app);

        return $app;
    }

    private function configureTestEnvironment(Application $app): void
    {
        $app['env'] = 'testing';

        $app['config']->set('app.env', 'testing');
        $app['config']->set('cache.default', 'array');
        $app['config']->set('mail.default', 'array');
        $app['config']->set('queue.default', 'sync');
        $app['config']->set('session.driver', 'array');

        if (extension_loaded('pdo_sqlite') && extension_loaded('sqlite3')) {
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.database', ':memory:');
            $app['config']->set('database.connections.sqlite.foreign_key_constraints', true);

            return;
        }

        if (extension_loaded('pdo_mysql')) {
            $app['config']->set('database.default', 'mysql');
            $app['config']->set('database.connections.mysql.host', 'mysql');
            $app['config']->set('database.connections.mysql.port', '3306');
            $app['config']->set('database.connections.mysql.database', 'testing');
            $app['config']->set('database.connections.mysql.username', 'sail');
            $app['config']->set('database.connections.mysql.password', 'password');

            return;
        }

        throw new RuntimeException('No supported database driver is available for the test suite.');
    }
}
