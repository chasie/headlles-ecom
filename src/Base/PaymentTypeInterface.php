<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Base\DataTransferObjects\PaymentAuthorize;
use HeadlessEcom\Base\DataTransferObjects\PaymentCapture;
use HeadlessEcom\Base\DataTransferObjects\PaymentRefund;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\Transaction;

interface PaymentTypeInterface
{
    /**
     * Set the cart.
     *
     * @param  \HeadlessEcom\Models\Cart  $order
     */
    public function cart(Cart $cart): self;

    /**
     * Set the order.
     */
    public function order(Order $order): self;

    /**
     * Set any data the provider might need.
     */
    public function withData(array $data): self;

    /**
     * Set any configuration on the driver.
     */
    public function setConfig(array $config): self;

    /**
     * Authorize the payment.
     *
     * @return void
     */
    public function authorize(): PaymentAuthorize;

    /**
     * Refund a transaction for a given amount.
     *
     * @param  null|string  $notes
     */
    public function refund(Transaction $transaction, int $amount, $notes = null): PaymentRefund;

    /**
     * Capture an amount for a transaction.
     *
     * @param  int  $amount
     */
    public function capture(Transaction $transaction, $amount = 0): PaymentCapture;
}
