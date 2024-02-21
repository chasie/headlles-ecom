<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Base\Purchasable;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Utils\Arr;

class GetExistingCartLine extends AbstractAction
{
    /**
     * Execute the action
     */
    public function execute(
        Cart $cart,
        Purchasable $purchasable,
        array $meta = []
    ): ?CartLine {
        // Get all possible cart lines
        $lines = $cart->lines()
            ->wherePurchasableType(
                $purchasable->getMorphClass()
            )->wherePurchasableId($purchasable->id)
            ->get();

        return $lines->first(function ($line) use ($meta) {
            $diff = Arr::diff($line->meta, $meta);

            return empty($diff->new) && empty($diff->edited) & empty($diff->removed);
        });
    }
}
