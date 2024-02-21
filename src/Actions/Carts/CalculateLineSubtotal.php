<?php

namespace HeadlessEcom\Actions\Carts;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use HeadlessEcom\Base\CartLineModifiers;
use HeadlessEcom\DataTypes\Price;
use HeadlessEcom\Facades\Pricing;
use HeadlessEcom\Models\CartLine;

class CalculateLineSubtotal
{
    /**
     * Execute the action.
     *
     * @param  \Illuminate\Database\Eloquent\Collection  $customerGroups
     * @return \HeadlessEcom\Models\CartLine
     */
    public function execute(
        CartLine $cartLine,
        Collection $customerGroups
    ) {
        $purchasable = $cartLine->purchasable;
        $cart = $cartLine->cart;
        $unitQuantity = $purchasable->getUnitQuantity();

        // we check if any cart line modifiers have already specified a unit price in their calculating() method
        if (! ($price = $cartLine->unitPrice) instanceof Price) {
            $priceResponse = Pricing::currency($cart->currency)
                ->qty($cartLine->quantity)
                ->currency($cart->currency)
                ->customerGroups($customerGroups)
                ->for($purchasable)
                ->get();

            $price = new Price(
                $priceResponse->matched->price->value,
                $cart->currency,
                $purchasable->getUnitQuantity()
            );
        }

        $unitPrice = (int) round(
            (($price->decimal / $purchasable->getUnitQuantity())
            * $cart->currency->factor),
            $cart->currency->decimal_places
        );

        $cartLine->subTotal = new Price($unitPrice * $cartLine->quantity, $cart->currency, $unitQuantity);
        $cartLine->unitPrice = new Price($unitPrice, $cart->currency, $unitQuantity);

        $pipeline = app(Pipeline::class)
            ->through(
                $this->getModifiers()->toArray()
            );

        return $pipeline->send($cartLine)->via('subtotalled')->thenReturn();
    }

    /**
     * Return the cart line modifiers.
     *
     * @return \Illuminate\Support\Collection
     */
    private function getModifiers()
    {
        return app(CartLineModifiers::class)->getModifiers();
    }
}
