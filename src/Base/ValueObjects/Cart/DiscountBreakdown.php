<?php

namespace HeadlessEcom\Base\ValueObjects\Cart;

use Illuminate\Support\Collection;
use HeadlessEcom\DataTypes\Price;
use HeadlessEcom\Models\Discount;

class DiscountBreakdown
{
    public function __construct(
        public Price $price,
        public Collection $lines,
        public Discount $discount,
    ) {
        //
    }
}
