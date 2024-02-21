<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Models\Cart;

abstract class ShippingModifier
{
    /**
     * Called just before cart totals are calculated.
     *
     * @return void
     */
    public function handle(Cart $cart)
    {
        //
    }
}
