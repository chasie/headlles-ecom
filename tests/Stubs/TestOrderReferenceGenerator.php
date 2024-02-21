<?php

namespace HeadlessEcom\Tests\Stubs;

use HeadlessEcom\Base\OrderReferenceGeneratorInterface;
use HeadlessEcom\Models\Order;

class TestOrderReferenceGenerator implements OrderReferenceGeneratorInterface
{
    /**
     * Called just after cart totals are calculated.
     *
     * @param  Order  $order
     * @return string
     */
    public function generate(Order $order): string
    {
        return 'reference-'.$order->id;
    }
}
