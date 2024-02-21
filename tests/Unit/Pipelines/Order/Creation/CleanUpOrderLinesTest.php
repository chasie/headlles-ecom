<?php

namespace HeadlessEcom\Tests\Unit\Pipelines\Order\Creation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\DataTypes\Price;
use HeadlessEcom\DataTypes\ShippingOption;
use HeadlessEcom\Facades\ShippingManifest;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartAddress;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\OrderLine;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Models\TaxClass;
use HeadlessEcom\Pipelines\Order\Creation\CleanUpOrderLines;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.orders.pipelines
 */
class CleanUpOrderLinesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_run_pipeline()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        ShippingManifest::addOption(
            new ShippingOption(
                name: 'Basic Delivery',
                description: 'Basic Delivery',
                identifier: 'BASDEL',
                price: new Price(500, $cart->currency, 1),
                taxClass: TaxClass::factory()->create()
            )
        );

        CartAddress::factory()->create([
            'type' => 'shipping',
            'shipping_option' => 'BASDEL',
            'cart_id' => $cart->id,
        ]);

        $order = Order::factory()->create([
            'cart_id' => $cart->id,
        ]);

        $purchasable = ProductVariant::factory()->create();
        $purchasableB = ProductVariant::factory()->create();

        \HeadlessEcom\Models\Price::factory()->create([
            'price' => 100,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($purchasable),
            'priceable_id' => $purchasable->id,
        ]);

        \HeadlessEcom\Models\Price::factory()->create([
            'price' => 100,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($purchasableB),
            'priceable_id' => $purchasableB->id,
        ]);

        $cart->lines()->create([
            'purchasable_type' => get_class($purchasable),
            'purchasable_id' => $purchasable->id,
            'quantity' => 1,
        ]);

        OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_id' => $purchasable->id,
            'purchasable_type' => get_class($purchasable),
        ]);

        OrderLine::factory()->create([
            'order_id' => $order->id,
            'purchasable_id' => $purchasableB->id,
            'purchasable_type' => get_class($purchasableB),
        ]);

        OrderLine::factory()->create([
            'identifier' => 'BASDEL',
            'purchasable_type' => ShippingOption::class,
            'type' => 'shipping',
            'order_id' => $order->id,
        ]);

        $order = app(CleanUpOrderLines::class)->handle($order, function ($order) {
            return $order;
        });

        $this->assertCount(1, $order->productLines);
        $this->assertEquals('BASDEL', $order->shippingLines->first()->identifier);
    }
}
