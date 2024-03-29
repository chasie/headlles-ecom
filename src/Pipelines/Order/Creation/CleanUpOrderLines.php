<?php

namespace HeadlessEcom\Pipelines\Order\Creation;

use Closure;
use HeadlessEcom\Models\Order;

class CleanUpOrderLines
{
    /**
     * @return Closure
     */
    public function handle(Order $order, Closure $next)
    {
        $cart = $order->cart;

        $purchasableIds = $cart->lines->pluck('purchasable_id');

        $order->productLines()
            ->whereNotIn('purchasable_id', $purchasableIds)
            ->delete();

        return $next($order);
    }
}
