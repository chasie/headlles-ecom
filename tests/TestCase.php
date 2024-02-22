<?php

namespace HeadlessEcom\Tests;

use Cartalyst\Converter\Laravel\ConverterServiceProvider;
use Illuminate\Support\Facades\Config;
use Kalnoy\Nestedset\NestedSetServiceProvider;
use HeadlessEcom\Facades\Taxes;
use HeadlessEcom\HeadlessEcomServiceProvider;
use HeadlessEcom\Tests\Stubs\TestTaxDriver;
use HeadlessEcom\Tests\Stubs\TestUrlGenerator;
use HeadlessEcom\Tests\Stubs\User;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\LaravelBlink\BlinkServiceProvider;
use Spatie\MediaLibrary\MediaLibraryServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // additional setup
        Config::set('providers.users.model', User::class);
        Config::set('headless-ecom.urls.generator', TestUrlGenerator::class);
        Config::set('headless-ecom.taxes.driver', 'test');
//        Config::set('scout.prefix', config('headless-ecom.database.table_prefix'));

        Taxes::extend(
            'test',
            function ($app)
            {
                return $app->make(TestTaxDriver::class);
            }
        );

        activity()->disableLogging();

        // Freeze time to avoid timestamp errors
        $this->freezeTime();
    }

    protected function getPackageProviders($app): array
    {
        return [
            HeadlessEcomServiceProvider::class,
            MediaLibraryServiceProvider::class,
            ActivitylogServiceProvider::class,
            ConverterServiceProvider::class,
            NestedSetServiceProvider::class,
            BlinkServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }

    /**
     * Define database migrations.
     *
     * @return void
     */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadLaravelMigrations();
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        $this->artisan('migrate')->run();
    }
}
