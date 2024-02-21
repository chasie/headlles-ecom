<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Models\Order;

interface OrderReferenceGeneratorInterface
{
    /**
     * Generate a reference for the order.
     */
    public function generate(Order $order): string;
}
