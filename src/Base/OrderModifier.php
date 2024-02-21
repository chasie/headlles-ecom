<?php

namespace HeadlessEcom\Base;

use Closure;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Order;

abstract class OrderModifier
{
    public function creating(Cart $cart, Closure $next): Cart
    {
        return $next($cart);
    }

    public function created(Order $order, Closure $next): Order
    {
        return $next($order);
    }
}
