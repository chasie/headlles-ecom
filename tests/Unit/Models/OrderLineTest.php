<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Exceptions\NonPurchasableItemException;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\OrderLine;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.orderlines
 */
class OrderLineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_an_order_line()
    {
        $order = Order::factory()->create();

        Currency::factory()->create([
            'default' => true,
        ]);

        $data = [
            'order_id' => $order->id,
            'quantity' => 1,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => ProductVariant::factory()->create()->id,
        ];

        OrderLine::factory()->create($data);

        $this->assertDatabaseHas(
            (new OrderLine())->getTable(),
            $data
        );
    }

    /** @test */
    public function check_unit_price_casts_correctly()
    {
        $order = Order::factory()->create();

        Currency::factory()->create([
            'default' => true,
        ]);

        $data = [
            'order_id' => $order->id,
            'quantity' => 1,
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => ProductVariant::factory()->create()->id,
            'unit_price' => 507,
            'unit_quantity' => 100,
        ];

        $orderLine = OrderLine::factory()->create($data);

        $this->assertDatabaseHas(
            (new OrderLine())->getTable(),
            $data
        );

        $this->assertEquals(5.07, $orderLine->unit_price->decimal);
        $this->assertEquals(0.05, $orderLine->unit_price->unitDecimal);
        $this->assertEquals(0.0507, $orderLine->unit_price->unitDecimal(false));
    }

    /** @test */
    public function only_purchasables_can_be_added_to_an_order()
    {
        $order = Order::factory()->create();

        $this->expectException(NonPurchasableItemException::class);

        $data = [
            'order_id' => $order->id,
            'quantity' => 1,
            'purchasable_type' => Channel::class,
            'purchasable_id' => Channel::factory()->create()->id,
        ];

        OrderLine::factory()->create($data);

        $this->assertDatabaseMissing((new CartLine())->getTable(), $data);
    }
}
