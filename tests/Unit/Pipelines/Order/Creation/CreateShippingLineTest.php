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
use HeadlessEcom\Models\TaxClass;
use HeadlessEcom\Pipelines\Order\Creation\CreateShippingLine;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.orders.pipelines
 */
class CreateShippingLineTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_run_pipeline()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        CartAddress::factory()->create([
            'type' => 'billing',
            'cart_id' => $cart->id,
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

        $order = app(CreateShippingLine::class)->handle($order, function ($order) {
            return $order;
        });

        $this->assertCount(1, $order->shippingLines);
        $this->assertEquals('BASDEL', $order->shippingLines->first()->identifier);
    }

    /** @test */
    public function can_update_shipping_line_if_exists()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        CartAddress::factory()->create([
            'type' => 'billing',
            'cart_id' => $cart->id,
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

        OrderLine::factory()->create([
            'identifier' => 'BASDEL',
            'purchasable_type' => ShippingOption::class,
            'type' => 'shipping',
            'order_id' => $order->id,
        ]);

        $order = app(CreateShippingLine::class)->handle($order->refresh(), function ($order) {
            return $order;
        });

        $this->assertCount(1, $order->shippingLines);
        $this->assertEquals('BASDEL', $order->shippingLines->first()->identifier);
    }
}
