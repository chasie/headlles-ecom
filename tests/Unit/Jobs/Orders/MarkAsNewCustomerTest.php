<?php

namespace HeadlessEcom\Tests\Unit\Jobs\Collections;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Jobs\Orders\MarkAsNewCustomer;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\OrderAddress;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.jobs.orders
 */
class MarkAsNewCustomerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_correctly_mark_order_for_new_customer()
    {
        Currency::factory()->create([
            'default' => true,
        ]);

        $order = Order::factory()->create([
            'new_customer' => false,
            'placed_at' => now()->subYear(),
        ]);

        OrderAddress::factory()->create([
            'order_id' => $order->id,
            'contact_email' => 'customer@site.com',
            'type' => 'billing',
        ]);

        MarkAsNewCustomer::dispatch($order->id);

        $this->assertTrue($order->refresh()->new_customer);

        $order = Order::factory()->create([
            'new_customer' => false,
            'placed_at' => now(),
        ]);

        OrderAddress::factory()->create([
            'order_id' => $order->id,
            'contact_email' => 'customer@site.com',
            'type' => 'billing',
        ]);

        MarkAsNewCustomer::dispatch($order->id);

        $this->assertFalse($order->refresh()->new_customer);
    }
}
