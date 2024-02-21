<?php

use HeadlessEcom\Base\OrderReferenceGenerator;

return [

    /*
    |--------------------------------------------------------------------------
    | Order Reference Generator
    |--------------------------------------------------------------------------
    |
    | Here you can specify how you want your order references to be generated
    | when you create an order from a cart.
    |
    */
    'reference_generator' => OrderReferenceGenerator::class,

    /*
    |--------------------------------------------------------------------------
    | Draft Status
    |--------------------------------------------------------------------------
    |
    | When a draft order is created from a cart, we need an initial status for
    | the order that's created. Define that here, it can be anything that would
    | make sense for the store you're building.
    |
    */
    'draft_status' => 'awaiting-payment',

    'statuses' => [
        'awaiting-payment' => [
            'label' => 'Ожидание платежа',
            'color' => '#848a8c',
            'mailers' => [],
            'notifications' => [],
        ],

        'payment-received' => [
            'label' => 'Платеж получен',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'collecting' => [
            'label' => 'Собирается',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'collected' => [
            'label' => 'Собран',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'shipment-waiting' => [
            'label' => 'Ожидает отправки',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'shipment-start' => [
            'label' => 'Отправлен',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'shipment-end' => [
            'label' => 'Прибыл',
            'color' => '#6a67ce',
            'mailers' => [],
            'notifications' => [],
        ],

        'ended' => [
            'label' => 'Завершен',
            'mailers' => [],
            'notifications' => [],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Order Pipelines
    |--------------------------------------------------------------------------
    |
    | Define which pipelines should be run throughout an orders lifecycle.
    | The default ones provided should suit most needs, however you are
    | free to add your own as you see fit.
    |
    | Each pipeline class will be run from top to bottom.
    |
    */
    'pipelines' => [
        'creation' => [
            HeadlessEcom\Pipelines\Order\Creation\FillOrderFromCart::class,
            HeadlessEcom\Pipelines\Order\Creation\CreateOrderLines::class,
            HeadlessEcom\Pipelines\Order\Creation\CreateOrderAddresses::class,
            HeadlessEcom\Pipelines\Order\Creation\CreateShippingLine::class,
            HeadlessEcom\Pipelines\Order\Creation\CleanUpOrderLines::class,
            HeadlessEcom\Pipelines\Order\Creation\MapDiscountBreakdown::class,
        ],
    ],

];
