<?php

namespace JarirAhmed\AuthMicroservice\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use JarirAhmed\AuthMicroservice\AuthMicroserviceServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app): array
    {
        return [AuthMicroserviceServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
            'foreign_key_constraints' => false,
        ]);
        $app['config']->set('auth-microservice.user_model', \JarirAhmed\AuthMicroservice\Models\User::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        // Create the users table first so our ALTER TABLE migration can run
        $this->app['db']->connection()->getSchemaBuilder()->create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
