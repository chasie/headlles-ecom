<?php

namespace HeadlessEcom\Actions\Orders;

use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class GenerateOrderReference
{
    /**
     * Execute the action.
     *
     * @param  CartLine  $cartLine
     * @param  Collection  $customerGroups
     * @return null|CartLine
     */
    public function execute(
        Order $order
    ): ?CartLine {
        $generator = config('headless-ecom.orders.reference_generator');

        if (! $generator) {
            return null;
        }

        return app($generator)->generate($order);
    }
}
