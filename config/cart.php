<?php

use HeadlessEcom\Actions\Carts\GenerateFingerprint;

return [

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | Specify the session key used when fetching the cart.
    |
    */
    'session_key' => 'headless_ecom_cart',

    /*
    |--------------------------------------------------------------------------
    | Fingerprint Generator
    |--------------------------------------------------------------------------
    |
    | Specify which class should be used when generating a cart fingerprint.
    |
    */
    'fingerprint_generator' => GenerateFingerprint::class,

    /*
    |--------------------------------------------------------------------------
    | Auto create a cart when none exists for user.
    |--------------------------------------------------------------------------
    |
    | Determines whether you want to automatically create a cart for a user if
    | they do not currently have one in the session. By default this is false
    | to minimise the amount of cart lines added to the database.
    |
    */
    'auto_create' => false,

    /*
    |--------------------------------------------------------------------------
    | Authentication policy
    |--------------------------------------------------------------------------
    |
    | When a user logs in, by default, HeadlessEcom will merge the current (guest) cart
    | with the users current cart, if they have one.
    | Available options: 'merge', 'override'
    |
    */
    'auth_policy' => 'merge',

    /*
    |--------------------------------------------------------------------------
    | Cart Pipelines
    |--------------------------------------------------------------------------
    |
    | Define which pipelines should be run when performing cart calculations.
    | The default ones provided should suit most needs, however you are
    | free to add your own as you see fit.
    |
    | Each pipeline class will be run from top to bottom.
    |
    */
    'pipelines' => [
        /*
         * Run these pipelines when the cart is calculating.
        */
        'cart' => [
            HeadlessEcom\Pipelines\Cart\CalculateLines::class,
            HeadlessEcom\Pipelines\Cart\ApplyShipping::class,
            HeadlessEcom\Pipelines\Cart\ApplyDiscounts::class,
            HeadlessEcom\Pipelines\Cart\CalculateTax::class,
            HeadlessEcom\Pipelines\Cart\Calculate::class,
        ],

        /*
         * Run these pipelines when the cart lines are being calculated.
        */
        'cart_lines' => [
            HeadlessEcom\Pipelines\CartLine\GetUnitPrice::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Actions
    |--------------------------------------------------------------------------
    |
    | Here you can decide what action should be run during a Carts lifecycle.
    | The default actions should be fine for most cases.
    |
    */
    'actions' => [
        'add_to_cart' => HeadlessEcom\Actions\Carts\AddOrUpdatePurchasable::class,
        'get_existing_cart_line' => HeadlessEcom\Actions\Carts\GetExistingCartLine::class,
        'update_cart_line' => HeadlessEcom\Actions\Carts\UpdateCartLine::class,
        'remove_from_cart' => HeadlessEcom\Actions\Carts\RemovePurchasable::class,
        'add_address' => HeadlessEcom\Actions\Carts\AddAddress::class,
        'set_shipping_option' => HeadlessEcom\Actions\Carts\SetShippingOption::class,
        'order_create' => HeadlessEcom\Actions\Carts\CreateOrder::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Action Validators
    |--------------------------------------------------------------------------
    |
    | You may wish to provide additional validation when actions executed on
    | the cart model. The defaults provided should be enough for most cases.
    |
    */
    'validators' => [

        'add_to_cart' => [
            HeadlessEcom\Validation\CartLine\CartLineQuantity::class,
        ],

        'update_cart_line' => [
            HeadlessEcom\Validation\CartLine\CartLineQuantity::class,
        ],

        'remove_from_cart' => [],

        'set_shipping_option' => [
            HeadlessEcom\Validation\Cart\ShippingOptionValidator::class,
        ],

        'order_create' => [
            HeadlessEcom\Validation\Cart\ValidateCartForOrderCreation::class,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Default eager loading
    |--------------------------------------------------------------------------
    |
    | When loading up a cart and doing calculations, there's a few relationships
    | that are used when it's running. Here you can define which relationships
    | should be eager loaded when these calculations take place.
    |
    */
    'eager_load' => [
        'currency',
        'lines.purchasable.taxClass',
        'lines.purchasable.values',
        'lines.purchasable.product.thumbnail',
        'lines.purchasable.prices.currency',
        'lines.purchasable.prices.priceable',
        'lines.purchasable.product',
        'lines.cart.currency',
    ],
];
