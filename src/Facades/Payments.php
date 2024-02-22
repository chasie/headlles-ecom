<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\Facade;
use HeadlessEcom\Base\PaymentManagerInterface;

class Payments extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return PaymentManagerInterface::class;
    }
}
