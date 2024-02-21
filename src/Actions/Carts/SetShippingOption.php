<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\DataTypes\ShippingOption;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;

class SetShippingOption extends AbstractAction
{
    /**
     * Execute the action.
     *
     * @param  CartLine  $cartLine
     * @param  ShippingOption  $customerGroups
     */
    public function execute(
        Cart $cart,
        ShippingOption $shippingOption
    ): self {
        $cart->shippingAddress->shippingOption = $shippingOption;
        $cart->shippingAddress->update([
            'shipping_option' => $shippingOption->getIdentifier(),
        ]);

        return $this;
    }
}
