<?php

namespace HeadlessEcom\Exceptions;

use Exception;

class DisallowMultipleCartOrdersException extends Exception
{
    public function __construct()
    {
        $this->message = __('headless-ecom::exceptions.disallow_multiple_cart_orders');
    }
}
