<?php

namespace HeadlessEcom\Exceptions;

use Exception;

class NonPurchasableItemException extends Exception
{
    public function __construct(string $classname)
    {
        $this->message = __('headless-ecom::exceptions.non_purchasable_item', [
            'class' => $classname,
        ]);
    }
}
