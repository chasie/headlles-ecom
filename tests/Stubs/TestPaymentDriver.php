<?php

namespace HeadlessEcom\Tests\Stubs;

use HeadlessEcom\Base\DataTransferObjects\PaymentAuthorize;
use HeadlessEcom\Base\DataTransferObjects\PaymentCapture;
use HeadlessEcom\Base\DataTransferObjects\PaymentRefund;
use HeadlessEcom\Models\Transaction;
use HeadlessEcom\PaymentTypes\AbstractPayment;

class TestPaymentDriver extends AbstractPayment
{
    /**
     * {@inheritDoc}
     */
    public function authorize(): PaymentAuthorize
    {
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
