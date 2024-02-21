<?php

namespace HeadlessEcom\Tests\Stubs;

use HeadlessEcom\DiscountTypes\AbstractDiscountType;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;

class TestDiscountType extends AbstractDiscountType
{
    /**
     * Return the name of the discount.
     */
    public function getName(): string
    {
        return 'Test Discount Type';
    }

    /**
     * Called just before cart totals are calculated.
     *
     * @return CartLine
     */
    public function apply(Cart $cart): Cart
    {
        return $cart;
    }
}
