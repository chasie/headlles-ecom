<?php

namespace HeadlessEcom;

use Illuminate\Support\ServiceProvider;
use HeadlessEcom\Addons\Manifest;
use HeadlessEcom\Base\AttributeManifest;
use HeadlessEcom\Base\AttributeManifestInterface;
use HeadlessEcom\Base\CartLineModifiers;
use HeadlessEcom\Base\CartModifiers;
use HeadlessEcom\Base\CartSessionInterface;
use HeadlessEcom\Base\DiscountManagerInterface;
use HeadlessEcom\Base\FieldTypeManifest;
use HeadlessEcom\Base\FieldTypeManifestInterface;
use HeadlessEcom\Base\ModelManifest;
use HeadlessEcom\Base\ModelManifestInterface;
use HeadlessEcom\Base\OrderModifiers;
use HeadlessEcom\Base\OrderReferenceGenerator;
use HeadlessEcom\Base\OrderReferenceGeneratorInterface;
use HeadlessEcom\Base\PaymentManagerInterface;
use HeadlessEcom\Base\PricingManagerInterface;
use HeadlessEcom\Base\ShippingManifest;
use HeadlessEcom\Base\ShippingManifestInterface;
use HeadlessEcom\Base\ShippingModifiers;
use HeadlessEcom\Base\StorefrontSessionInterface;
use HeadlessEcom\Base\TaxManagerInterface;
use HeadlessEcom\Console\Commands\AddonsDiscover;
use HeadlessEcom\Console\Commands\Import\AddressData;
use HeadlessEcom\Console\Commands\MigrateGetCandy;
use HeadlessEcom\Console\Commands\Orders\SyncNewCustomerOrders;
use HeadlessEcom\Console\Commands\ScoutIndexerCommand;
use HeadlessEcom\Console\InstallHeadlessEcom;
use HeadlessEcom\Database\State\ConvertProductTypeAttributesToProducts;
use HeadlessEcom\Database\State\ConvertTaxbreakdown;
use HeadlessEcom\Database\State\EnsureBrandsAreUpgraded;
use HeadlessEcom\Database\State\EnsureDefaultTaxClassExists;
use HeadlessEcom\Database\State\EnsureMediaCollectionsAreRenamed;
use HeadlessEcom\Database\State\MigrateCartOrderRelationship;
use HeadlessEcom\Database\State\PopulateProductOptionLabelWithName;
use HeadlessEcom\Listeners\CartSessionAuthListener;
use HeadlessEcom\Managers\CartSessionManager;
use HeadlessEcom\Managers\DiscountManager;
use HeadlessEcom\Managers\PaymentManager;
use HeadlessEcom\Managers\PricingManager;
use HeadlessEcom\Managers\StorefrontSessionManager;
use HeadlessEcom\Managers\TaxManager;
use HeadlessEcom\Models\Address;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\CustomerGroup;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\OrderLine;
use HeadlessEcom\Models\Transaction;
use HeadlessEcom\Models\Url;
use HeadlessEcom\Observers\AddressObserver;
use HeadlessEcom\Observers\CartLineObserver;
use HeadlessEcom\Observers\ChannelObserver;
use HeadlessEcom\Observers\CollectionObserver;
use HeadlessEcom\Observers\CurrencyObserver;
use HeadlessEcom\Observers\CustomerGroupObserver;
use HeadlessEcom\Observers\LanguageObserver;
use HeadlessEcom\Observers\OrderLineObserver;
use HeadlessEcom\Observers\OrderObserver;
use HeadlessEcom\Observers\TransactionObserver;
use HeadlessEcom\Observers\UrlObserver;

class HeadlesEcomServiceProvider extends ServiceProvider
{
    protected $configFiles = [
        'cart',
        'database',
        'media',
        'orders',
        'payments',
        'pricing',
        'search',
        'shipping',
        'taxes',
        'urls',
    ];

    protected $root = __DIR__.'/..';

    /**
     * Register any application services.
     */
    public function register(): void
    {
        collect($this->configFiles)->each(function ($config) {
            $this->mergeConfigFrom("{$this->root}/config/$config.php", "headless-ecom.$config");
        });

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'headless-ecom');

        $this->registerAddonManifest();

        $this->app->singleton(CartModifiers::class, function () {
            return new CartModifiers();
        });

        $this->app->singleton(CartLineModifiers::class, function () {
            return new CartLineModifiers();
        });

        $this->app->singleton(OrderModifiers::class, function () {
            return new OrderModifiers();
        });

        $this->app->singleton(CartSessionInterface::class, function ($app) {
            return $app->make(CartSessionManager::class);
        });

        $this->app->singleton(StorefrontSessionInterface::class, function ($app) {
            return $app->make(StorefrontSessionManager::class);
        });

        $this->app->singleton(ShippingModifiers::class, function ($app) {
            return new ShippingModifiers();
        });

        $this->app->singleton(ShippingManifestInterface::class, function ($app) {
            return $app->make(ShippingManifest::class);
        });

        $this->app->singleton(OrderReferenceGeneratorInterface::class, function ($app) {
            return $app->make(OrderReferenceGenerator::class);
        });

        $this->app->singleton(AttributeManifestInterface::class, function ($app) {
            return $app->make(AttributeManifest::class);
        });

        $this->app->singleton(FieldTypeManifestInterface::class, function ($app) {
            return $app->make(FieldTypeManifest::class);
        });

        $this->app->singleton(ModelManifestInterface::class, function ($app) {
            return $app->make(ModelManifest::class);
        });

        $this->app->bind(PricingManagerInterface::class, function ($app) {
            return $app->make(PricingManager::class);
        });

        $this->app->singleton(TaxManagerInterface::class, function ($app) {
            return $app->make(TaxManager::class);
        });

        $this->app->singleton(PaymentManagerInterface::class, function ($app) {
            return $app->make(PaymentManager::class);
        });

        $this->app->singleton(DiscountManagerInterface::class, function ($app) {
            return $app->make(DiscountManager::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! config('headless-ecom.database.disable_migrations', false)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }

        $this->registerObservers();
        $this->registerBlueprintMacros();
        $this->registerStateListeners();

        if ($this->app->runningInConsole()) {
            collect($this->configFiles)->each(function ($config) {
                $this->publishes(
                    [
                                     "{$this->root}/config/$config.php" => config_path("headless-ecom/$config.php"),
                                 ], 
                    'headless-ecom'
                );
            });

            $this->publishes(
                [
                                 __DIR__.'/../resources/lang' => lang_path('vendor/headless-ecom'),
                             ],
                'headless-ecom.translation'
            );

            $this->publishes(
                [
                                 __DIR__.'/../database/migrations/' => database_path('migrations'),
                             ], 
                'headless-ecom.migrations'
            );

            $this->commands(
                [
                                InstallHeadlessEcom::class,
                                AddonsDiscover::class,
                                AddressData::class,
                                ScoutIndexerCommand::class,
                                MigrateGetCandy::class,
                                SyncNewCustomerOrders::class,
                            ]
            );
        }

        Arr::macro('permutate', [\HeadlessEcom\Utils\Arr::class, 'permutate']);

        // Handle generator
        Str::macro('handle', function ($string) {
            return Str::slug($string, '_');
        });

        Converter::setMeasurements(
            config('headless-ecom.shipping.measurements', [])
        );

        Event::listen(
            Login::class,
            [CartSessionAuthListener::class, 'login']
        );

        Event::listen(
            Logout::class,
            [CartSessionAuthListener::class, 'logout']
        );
    }

    protected function registerAddonManifest()
    {
        $this->app->instance(Manifest::class, new Manifest(
            new Filesystem(),
            $this->app->basePath(),
            $this->app->bootstrapPath().'/cache/headless-ecom_addons.php'
        ));
    }

    protected function registerStateListeners()
    {
        $states = [
            ConvertProductTypeAttributesToProducts::class,
            EnsureDefaultTaxClassExists::class,
            EnsureBrandsAreUpgraded::class,
            EnsureMediaCollectionsAreRenamed::class,
            PopulateProductOptionLabelWithName::class,
            MigrateCartOrderRelationship::class,
            ConvertTaxbreakdown::class,
        ];

        foreach ($states as $state) {
            $class = new $state;

            Event::listen(
                [MigrationsStarted::class],
                [$class, 'prepare']
            );

            Event::listen(
                [MigrationsEnded::class, NoPendingMigrations::class],
                [$class, 'run']
            );
        }
    }

    /**
     * Register the observers used in HeadlessEcom.
     */
    protected function registerObservers(): void
    {
        Channel::observe(ChannelObserver::class);
        CustomerGroup::observe(CustomerGroupObserver::class);
        Language::observe(LanguageObserver::class);
        Currency::observe(CurrencyObserver::class);
        Url::observe(UrlObserver::class);
        Collection::observe(CollectionObserver::class);
        CartLine::observe(CartLineObserver::class);
        Order::observe(OrderObserver::class);
        OrderLine::observe(OrderLineObserver::class);
        Address::observe(AddressObserver::class);
        Transaction::observe(TransactionObserver::class);
    }

    /**
     * Register the blueprint macros.
     */
    protected function registerBlueprintMacros(): void
    {
        Blueprint::macro('scheduling', function () {
            /** @var Blueprint $this */
            $this->boolean('enabled')->default(false)->index();
            $this->timestamp('starts_at')->nullable()->index();
            $this->timestamp('ends_at')->nullable()->index();
        });

        Blueprint::macro('dimensions', function () {
            /** @var Blueprint $this */
            $columns = ['length', 'width', 'height', 'weight', 'volume'];
            foreach ($columns as $column) {
                $this->decimal("{$column}_value", 10, 4)->default(0)->nullable()->index();
                $this->string("{$column}_unit")->default('mm')->nullable();
            }
        });

        Blueprint::macro('userForeignKey', function ($field_name = 'user_id', $nullable = false) {
            /** @var Blueprint $this */
            $userModel = config('auth.providers.users.model');

            $type = config('headless-ecom.database.users_id_type', 'bigint');

            if ($type == 'uuid') {
                $this->foreignUuId($field_name)
                    ->nullable($nullable)
                    ->constrained(
                        (new $userModel())->getTable()
                    );
            } elseif ($type == 'int') {
                $this->unsignedInteger($field_name)->nullable($nullable);
                $this->foreign($field_name)->references('id')->on('users');
            } else {
                $this->foreignId($field_name)
                    ->nullable($nullable)
                    ->constrained(
                        (new $userModel())->getTable()
                    );
            }
        });
    }
}
