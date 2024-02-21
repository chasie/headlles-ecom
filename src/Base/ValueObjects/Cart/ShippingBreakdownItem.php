<?php

namespace HeadlessEcom\Base\ValueObjects\Cart;

use HeadlessEcom\DataTypes\Price;

class ShippingBreakdownItem
{
    public function __construct(
        public string $name,
        public string $identifier,
        public Price $price
    ) {
        //
    }
}
