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
     * @param  Order  $order
     * @return null|string
     */
    public function execute(
        Order $order
    ): ?string {
        $generator = config('headless-ecom.orders.reference_generator');

        if (! $generator) {
            return null;
        }

        return app($generator)->generate($order);
    }
}
