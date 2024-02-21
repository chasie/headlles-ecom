<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;

interface DiscountTypeInterface
{
    /**
     * Return the name of the discount type.
     */
    public function getName(): string;

    /**
     * Execute and apply the discount if conditions are met.
     *
     * @param  CartLine  $cartLine
     * @return CartLine
     */
    public function apply(Cart $cart): Cart;
}
