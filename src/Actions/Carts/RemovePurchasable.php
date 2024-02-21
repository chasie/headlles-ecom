<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Exceptions\CartLineIdMismatchException;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\Cart;

class RemovePurchasable extends AbstractAction
{
    /**
     * Execute the action
     *
     * @return bool
     *
     * @throws CartLineIdMismatchException
     */
    public function execute(
        Cart $cart,
        int $cartLineId
    ): self {
        DB::transaction(function () use ($cart, $cartLineId) {
            $line = $cart->lines()->whereId($cartLineId)->first();

            if (! $line) {
                // If we're trying to remove a line that does not
                // belong to this cart, throw an exception.
                throw new CartLineIdMismatchException(
                    __('headless-ecom::exceptions.cart_line_id_mismatch')
                );
            }

            $line->delete();
        });

        return $this;
    }
}
