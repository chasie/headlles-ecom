<?php

namespace HeadlessEcom\Base\DataTransferObjects;

use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\Discount;

class CartDiscount
{
    public function __construct(
        public CartLine|Cart $model,
        public Discount $discount
    ) {
        //
    }
}
