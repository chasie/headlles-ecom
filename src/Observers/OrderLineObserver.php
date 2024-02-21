<?php

namespace HeadlessEcom\Observers;

use HeadlessEcom\Base\Purchasable;
use HeadlessEcom\Exceptions\NonPurchasableItemException;
use HeadlessEcom\Models\OrderLine;

class OrderLineObserver
{
    /**
     * Handle the OrderLine "creating" event.
     *
     * @return void
     */
    public function creating(OrderLine $orderLine)
    {
        if ($orderLine->type != 'shipping' && ! $orderLine->purchasable instanceof Purchasable) {
            throw new NonPurchasableItemException($orderLine->purchasable_type);
        }
    }

    /**
     * Handle the OrderLine "updated" event.
     *
     * @return void
     */
    public function updating(OrderLine $orderLine)
    {
        if ($orderLine->type != 'shipping' && ! $orderLine->purchasable instanceof Purchasable) {
            throw new NonPurchasableItemException($orderLine->purchasable_type);
        }
    }
}
