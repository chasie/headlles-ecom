<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\Facade;
use HeadlessEcom\Base\CartSessionInterface;

class CartSession extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return CartSessionInterface::class;
    }
}
