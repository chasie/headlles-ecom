<?php

namespace HeadlessEcom\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\FieldTypes\TranslatedText;
use HeadlessEcom\Hub\AdminHubServiceProvider;
use HeadlessEcom\Models\Attribute;
use HeadlessEcom\Models\AttributeGroup;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\CollectionGroup;
use HeadlessEcom\Models\Country;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\CustomerGroup;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\ProductType;
use HeadlessEcom\Models\TaxClass;

class InstallHeadlessEcom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'headless-ecom:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the HeadlessEcom';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->newLine();
        $this->comment('Installing HeadlessEcom...');

        $this->newLine();
        $this->info('Publishing configuration...');

        if (! $this->configExists('headless-ecom')) {
            $this->publishConfiguration();
        } else {
            if ($this->shouldOverwriteConfig()) {
                $this->line('Overwriting configuration file...');
                $this->publishConfiguration($force = true);
            } else {
                $this->line('Existing configuration was not overwritten');
            }
        }

        if ($this->confirm('Run database migrations?', true)) {
            $this->call('migrate');
        }

        DB::transaction(function () {
            if (! Country::count()) {
                $this->info('Importing countries');
                $this->call('headless-ecom:import:address-data');
            }

            if (! Channel::whereDefault(true)->exists()) {
                $this->info('Setting up default channel');

                Channel::create([
                    'name' => 'Webstore',
                    'handle' => 'webstore',
                    'default' => true,
                    'url' => 'http://localhost',
                ]);
            }

            if (! Language::count()) {
                $this->info('Adding default language');

                Language::create([
                    'code' => 'en',
                    'name' => 'English',
                    'default' => true,
                ]);
            }

            if (! Currency::whereDefault(true)->exists()) {
                $this->info('Adding a default currency (USD)');

                Currency::create([
                    'code' => 'USD',
                    'name' => 'US Dollar',
                    'exchange_rate' => 1,
                    'decimal_places' => 2,
                    'default' => true,
                    'enabled' => true,
                ]);
            }

            if (! CustomerGroup::whereDefault(true)->exists()) {
                $this->info('Adding a default customer group.');

                CustomerGroup::create([
                    'name' => 'Retail',
                    'handle' => 'retail',
                    'default' => true,
                ]);
            }

            if (! CollectionGroup::count()) {
                $this->info('Adding an initial collection group');

                CollectionGroup::create([
                    'name' => 'Main',
                    'handle' => 'main',
                ]);
            }

            if (! TaxClass::count()) {
                $this->info('Adding a default tax class.');

                TaxClass::create([
                    'name' => 'Default Tax Class',
                    'default' => true,
                ]);
            }

            if (! Attribute::count()) {
                $this->info('Setting up initial attributes');

                $group = AttributeGroup::create([
                    'attributable_type' => Product::class,
                    'name' => collect([
                        'en' => 'Details',
                    ]),
                    'handle' => 'details',
                    'position' => 1,
                ]);

                $collectionGroup = AttributeGroup::create([
                    'attributable_type' => Collection::class,
                    'name' => collect([
                        'en' => 'Details',
                    ]),
                    'handle' => 'collection_details',
                    'position' => 1,
                ]);

                Attribute::create([
                    'attribute_type' => Product::class,
                    'attribute_group_id' => $group->id,
                    'position' => 1,
                    'name' => [
                        'en' => 'Name',
                    ],
                    'handle' => 'name',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => true,
                    'default_value' => null,
                    'configuration' => [
                        'richtext' => false,
                    ],
                    'system' => true,
                ]);

                Attribute::create([
                    'attribute_type' => Collection::class,
                    'attribute_group_id' => $collectionGroup->id,
                    'position' => 1,
                    'name' => [
                        'en' => 'Name',
                    ],
                    'handle' => 'name',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => true,
                    'default_value' => null,
                    'configuration' => [
                        'richtext' => false,
                    ],
                    'system' => true,
                ]);

                Attribute::create([
                    'attribute_type' => Product::class,
                    'attribute_group_id' => $group->id,
                    'position' => 2,
                    'name' => [
                        'en' => 'Description',
                    ],
                    'handle' => 'description',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => false,
                    'default_value' => null,
                    'configuration' => [
                        'richtext' => true,
                    ],
                    'system' => false,
                ]);

                Attribute::create([
                    'attribute_type' => Collection::class,
                    'attribute_group_id' => $collectionGroup->id,
                    'position' => 2,
                    'name' => [
                        'en' => 'Description',
                    ],
                    'handle' => 'description',
                    'section' => 'main',
                    'type' => TranslatedText::class,
                    'required' => false,
                    'default_value' => null,
                    'configuration' => [
                        'richtext' => true,
                    ],
                    'system' => false,
                ]);
            }

            if (! ProductType::count()) {
                $this->info('Adding a product type.');

                $type = ProductType::create([
                    'name' => 'Stock',
                ]);

                $type->mappedAttributes()->attach(
                    Attribute::whereAttributeType(Product::class)->get()->pluck('id')
                );
            }
        });

        if ($this->isHubInstalled()) {
            $this->newLine();
            $this->line('Installing Admin Hub.');
            $this->call('headless-ecom:hub:install');
        }

        $this->newLine();
        $this->comment('headless-ecom is now installed 🚀');
        $this->newLine();

        $this->line('Please show some love for HeadlessEcom by giving us a star on GitHub ⭐️');
        $this->info('https://github.com/HeadlessEcomphp/HeadlessEcom');
        $this->newLine(3);
    }

    /**
     * Checks if config exists given a filename.
     *
     * @param  string  $fileName
     */
    private function configExists($fileName): bool
    {
        if (! File::isDirectory(config_path($fileName))) {
            return false;
        }

        return ! empty(File::allFiles(config_path($fileName)));
    }

    /**
     * Returns a prompt if config exists and ask to override it.
     */
    private function shouldOverwriteConfig(): bool
    {
        return $this->confirm(
            'Config file already exists. Do you want to overwrite it?',
            false
        );
    }

    /**
     * Publishes configuration for the Service Provider.
     *
     * @param  bool  $forcePublish
     */
    private function publishConfiguration($forcePublish = false): void
    {
        $params = [
            '--provider' => "HeadlessEcom\HeadlessEcomServiceProvider",
            '--tag' => 'headless-ecom',
        ];

        if ($forcePublish === true) {
            $params['--force'] = true;
        }

        $this->call('vendor:publish', $params);
    }

    /**
     * Determines if the admin hub is installed.
     *
     * @return bool
     */
    private function isHubInstalled()
    {
        return class_exists(AdminHubServiceProvider::class);
    }
}
