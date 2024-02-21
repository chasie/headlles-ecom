<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\Facade;
use HeadlessEcom\Base\DiscountManagerInterface;

class Discounts extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return DiscountManagerInterface::class;
    }
}
