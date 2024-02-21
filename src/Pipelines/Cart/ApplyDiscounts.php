<?php

namespace HeadlessEcom\Pipelines\Cart;

use Closure;
use HeadlessEcom\Facades\Discounts;
use HeadlessEcom\Models\Cart;

final class ApplyDiscounts
{
    /**
     * Called just before cart totals are calculated.
     *
     * @return void
     */
    public function handle(Cart $cart, Closure $next)
    {
        $cart->discounts = collect([]);
        $cart->discountBreakdown = collect([]);

        Discounts::apply($cart);

        return $next($cart);
    }
}
