<?php

namespace HeadlessEcom\Base\DataTransferObjects;

class PaymentCapture
{
    public function __construct(
        public bool $success = false,
        public string $message = ''
    ) {
        //
    }
}
