<?php

namespace Chasie\HeadlesEcom;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Chasie\HeadlesEcom\Skeleton\SkeletonClass
 */
class HeadlesEcomFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'headles-ecom';
    }
}
