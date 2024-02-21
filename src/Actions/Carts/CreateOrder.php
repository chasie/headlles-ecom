<?php

namespace HeadlessEcom\Actions\Carts;

use Illuminate\Pipeline\Pipeline;
use HeadlessEcom\Actions\AbstractAction;
use HeadlessEcom\Exceptions\DisallowMultipleCartOrdersException;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Jobs\Orders\MarkAsNewCustomer;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Order;

final class CreateOrder extends AbstractAction
{
    /**
     * Execute the action.
     */
    public function execute(
        Cart $cart,
        bool $allowMultipleOrders = false,
        int $orderIdToUpdate = null
    ): self {
        $this->passThrough = DB::transaction(function () use ($cart, $allowMultipleOrders, $orderIdToUpdate) {
            $order = $cart->draftOrder($orderIdToUpdate)->first() ?: new Order;

            if ($cart->hasCompletedOrders() && ! $allowMultipleOrders) {
                throw new DisallowMultipleCartOrdersException;
            }

            $order->fill([
                'cart_id' => $cart->id,
            ]);

            $order = app(Pipeline::class)
                ->send($order)
                ->through(
                    config('headless-ecom.orders.pipelines.creation', [])
                )->thenReturn(function ($order) {
                    return $order;
                });

            $cart->discounts?->each(function ($discount) use ($cart) {
                $discount->markAsUsed($cart)->discount->save();
            });

            $cart->save();

            MarkAsNewCustomer::dispatch($order->id);

            $order->refresh();

            return $order;
        });

        return $this;
    }
}
