<?php

namespace HeadlessEcom\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use HeadlessEcom\Base\DataTransferObjects\PaymentAuthorize;

class PaymentAttemptEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public PaymentAuthorize $paymentAuthorize
    ) {
    }
}
