<?php

namespace HeadlessEcom\Actions\Carts;

use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\CartLine;
use Illuminate\Database\Eloquent\Collection;

class UpdateCartLine extends AbstractAction
{
    /**
     * Execute the action.
     *
     * @param  CartLine  $cartLine
     * @param  Collection  $customerGroups
     * @return CartLine
     */
    public function execute(
        int $cartLineId,
        int $quantity,
        $meta = null
    ): self {
        DB::transaction(function () use ($cartLineId, $quantity, $meta) {
            $data = [
                'quantity' => $quantity,
            ];

            if ($meta) {
                if (is_object($meta)) {
                    $meta = (array) $meta;
                }
                $data['meta'] = $meta;
            }

            CartLine::whereId($cartLineId)->update($data);
        });

        return $this;
    }
}
