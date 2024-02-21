<?php

namespace HeadlessEcom\Facades;

use Illuminate\Support\Facades\Facade;
use HeadlessEcom\Base\TaxManagerInterface;

class Taxes extends Facade
{
    public static function getFacadeAccessor()
    {
        return TaxManagerInterface::class;
    }
}
