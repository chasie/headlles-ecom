<?php

namespace HeadlessEcom\Tests\Unit\Actions\Carts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Carts\RemovePurchasable;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 * @group headless-ecom.actions.carts
 */
class RemovePurchasableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_remove_cart_line()
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

        $cart->add($purchasable, 1);

        $this->assertCount(1, $cart->refresh()->lines);

        $action = new RemovePurchasable;

        $action->execute($cart, $cart->lines->first()->id);

        $this->assertCount(0, $cart->refresh()->lines);
    }
}
