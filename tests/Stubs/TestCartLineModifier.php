<?php

namespace HeadlessEcom\Tests\Stubs;

use Closure;
use HeadlessEcom\Base\CartLineModifier;
use HeadlessEcom\DataTypes\Price;
use HeadlessEcom\Models\CartLine;

class TestCartLineModifier extends CartLineModifier
{
    public function calculating(CartLine $cartLine, Closure $next): CartLine
    {
        $cartLine->unitPrice = new Price(1000, $cartLine->cart->currency, 1);

        return $next($cartLine);
    }
}
