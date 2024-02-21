<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Models for indexing
    |--------------------------------------------------------------------------
    |
    | The model listed here will be used to create/populate the indexes.
    | You can provide your own model here to run them all on the same
    | search engine.
    |
    */
    'models' => [
        /*
         * These models are required by the system, do not change them.
         */
        HeadlessEcom\Models\Brand::class,
        HeadlessEcom\Models\Collection::class,
        HeadlessEcom\Models\Customer::class,
        HeadlessEcom\Models\Order::class,
        HeadlessEcom\Models\Product::class,
        HeadlessEcom\Models\ProductOption::class,

        /*
         * Below you can add your own models for indexing...
         */
        // App\Models\Example::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Search engine mapping
    |--------------------------------------------------------------------------
    |
    | You can define what search driver each searchable model should use.
    | If the model isn't defined here, it will use the SCOUT_DRIVER env variable.
    |
    */
    'engine_map' => [
        // HeadlessEcom\Models\Product::class => 'algolia',
        // HeadlessEcom\Models\Order::class => 'meilisearch',
        // HeadlessEcom\Models\Collection::class => 'meilisearch',
    ],

    'indexers' => [
        HeadlessEcom\Models\Brand::class => HeadlessEcom\Search\BrandIndexer::class,
        HeadlessEcom\Models\Collection::class => HeadlessEcom\Search\CollectionIndexer::class,
        HeadlessEcom\Models\Customer::class => HeadlessEcom\Search\CustomerIndexer::class,
        HeadlessEcom\Models\Order::class => HeadlessEcom\Search\OrderIndexer::class,
        HeadlessEcom\Models\Product::class => HeadlessEcom\Search\ProductIndexer::class,
        HeadlessEcom\Models\ProductOption::class => HeadlessEcom\Search\ProductOptionIndexer::class,
    ],

];
