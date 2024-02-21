<?php

namespace HeadlessEcom\PaymentTypes;

use HeadlessEcom\Base\DataTransferObjects\PaymentAuthorize;
use HeadlessEcom\Base\DataTransferObjects\PaymentCapture;
use HeadlessEcom\Base\DataTransferObjects\PaymentRefund;
use HeadlessEcom\Models\Transaction;

class OfflinePayment extends AbstractPayment
{
    /**
     * {@inheritDoc}
     */
    public function authorize(): PaymentAuthorize
    {
        if (! $this->order) {
            if (! $this->order = $this->cart->draftOrder()->first()) {
                $this->order = $this->cart->createOrder();
            }
        }
        $orderMeta = array_merge(
            (array) $this->order->meta,
            $this->data['meta'] ?? []
        );

        $status = $this->data['authorized'] ?? null;

        $this->order->update([
            'status' => $status ?? ($this->config['authorized'] ?? null),
            'meta' => $orderMeta,
            'placed_at' => now(),
        ]);

        return new PaymentAuthorize(true);
    }

    /**
     * {@inheritDoc}
     */
    public function refund(Transaction $transaction, int $amount = 0, $notes = null): PaymentRefund
    {
        return new PaymentRefund(true);
    }

    /**
     * {@inheritDoc}
     */
    public function capture(Transaction $transaction, $amount = 0): PaymentCapture
    {
        return new PaymentCapture(true);
    }
}
