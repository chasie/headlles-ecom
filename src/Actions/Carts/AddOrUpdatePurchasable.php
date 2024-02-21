<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Base\Purchasable;
use HeadlessEcom\Exceptions\InvalidCartLineQuantityException;
use HeadlessEcom\Models\Cart;

class AddOrUpdatePurchasable extends AbstractAction
{
    /**
     * Execute the action.
     *
     * @param  \HeadlessEcom\Models\CartLine  $cartLine
     * @param  \Illuminate\Database\Eloquent\Collection  $customerGroups
     * @return \HeadlessEcom\Models\CartLine
     */
    public function execute(
        Cart $cart,
        Purchasable $purchasable,
        int $quantity = 1,
        array $meta = []
    ): self {
        throw_if(! $quantity, InvalidCartLineQuantityException::class);

        $existing = app(
            config('headless-ecom.cart.actions.get_existing_cart_line', GetExistingCartLine::class)
        )->execute(
            cart: $cart,
            purchasable: $purchasable,
            meta: $meta
        );

        if ($existing) {
            $existing->update([
                'quantity' => $existing->quantity + $quantity,
            ]);

            return $this;
        }

        $cart->lines()->create([
            'purchasable_id' => $purchasable->id,
            'purchasable_type' => $purchasable->getMorphClass(),
            'quantity' => $quantity,
            'meta' => $meta,
        ]);

        return $this;
    }
}
