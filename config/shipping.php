<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Measurements
    |--------------------------------------------------------------------------
    |
    | You can use any measurements available at
    | https://github.com/cartalyst/converter/edit/master/src/config/config.php
    |
    */
    'measurements' => [

        'length' => [

            'm' => [
                'format' => '1,0.000 m',
                'unit' => 1.00,
            ],

            'mm' => [
                'format' => '1,0.000 mm',
                'unit' => 1000,
            ],

            'cm' => [
                'format' => '1!0 cm',
                'unit' => 100,
            ],

        ],

        'area' => [

            'sqm' => [
                'format' => '1,00.00 sq m',
                'unit' => 1,
            ],

        ],

        'weight' => [

            'kg' => [
                'format' => '1,0.00 kg',
                'unit' => 1.00,
            ],

            'g' => [
                'format' => '1,0.00 g',
                'unit' => 1000.00,
            ],

        ],

        'volume' => [

            'l' => [
                'format' => '1,00.00l',
                'unit' => 1,
            ],

            'ml' => [
                'format' => '1,00.000ml',
                'unit' => 1000,
            ],

        ],

    ],

];
