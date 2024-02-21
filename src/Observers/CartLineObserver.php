<?php

namespace HeadlessEcom\Observers;

use HeadlessEcom\Base\Purchasable;
use HeadlessEcom\Exceptions\NonPurchasableItemException;
use HeadlessEcom\Models\CartLine;

class CartLineObserver
{
    /**
     * Handle the CartLine "creating" event.
     *
     * @return void
     */
    public function creating(CartLine $cartLine)
    {
        if (! $cartLine->purchasable instanceof Purchasable) {
            throw new NonPurchasableItemException($cartLine->purchasable_type);
        }
    }

    /**
     * Handle the CartLine "updated" event.
     *
     * @return void
     */
    public function updating(CartLine $cartLine)
    {
        if (! $cartLine->purchasable instanceof Purchasable) {
            throw new NonPurchasableItemException($cartLine->purchasable_type);
        }
    }
}
