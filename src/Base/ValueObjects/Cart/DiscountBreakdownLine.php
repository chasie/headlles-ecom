<?php

namespace HeadlessEcom\Base\ValueObjects\Cart;

use HeadlessEcom\Models\CartLine;

class DiscountBreakdownLine
{
    public function __construct(
        public CartLine $line,
        public int $quantity,
    ) {
        //
    }
}
