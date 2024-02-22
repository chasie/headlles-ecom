<?php

namespace HeadlessEcom\Tests\Unit\Pipelines\Order\Creation;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Pipelines\Order\Creation\FillOrderFromCart;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.orders.pipelines
 */
class FillOrderFromCartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_run_pipeline()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $purchasable = ProductVariant::factory()->create();

        Price::factory()->create([
            'price' => 100,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($purchasable),
            'priceable_id' => $purchasable->id,
        ]);

        $cart->lines()->create([
            'purchasable_type' => get_class($purchasable),
            'purchasable_id' => $purchasable->id,
            'quantity' => 1,
        ]);

        $order = new Order([
            'cart_id' => $cart->id,
        ]);

        $cart->calculate();

        app(FillOrderFromCart::class)->handle($order, function ($order) {
            return $order;
        });

        $this->assertNotNull($order->reference);
        $this->assertEquals($cart->user_id, $order->user_id);
        $this->assertEquals($cart->channel_id, $order->channel_id);
        $this->assertEquals($cart->subTotal->value, $order->sub_total->value);
        $this->assertEquals($cart->discountTotal?->value, $order->discount_otal?->value);
        $this->assertEquals($cart->taxTotal->value, $order->tax_total->value);
        $this->assertEquals($cart->total->value, $order->total->value);
    }
}
