<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Models\Order;

interface OrderReferenceGeneratorInterface
{
    /**
     * Generate a reference for the order.
     *
     * @param  Order  $order
     * @return string
     */
    public function generate(Order $order): string;
}
