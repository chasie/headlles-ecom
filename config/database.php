<?php

return [

    'connection' => '',

    'table_prefix' => 'ecom_',

    /*
    |--------------------------------------------------------------------------
    | Users Table ID
    |--------------------------------------------------------------------------
    |
    | HeadlessEcom adds a relationship to your 'users' table and by default assumes
    | a 'bigint'. You can change this to either an 'int' or 'uuid'.
    |
    */
    'users_id_type' => 'bigint',

    /*
    |--------------------------------------------------------------------------
    | Disable migrations
    |--------------------------------------------------------------------------
    |
    | Prevent HeadlessEcom`s default package migrations from running for the core.
    | Set to 'true' to disable.
    |
    */
    'disable_migrations' => false,

];
