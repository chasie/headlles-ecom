<?php

namespace HeadlessEcom\Tests\Unit\Actions\Carts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Carts\AddOrUpdatePurchasable;
use HeadlessEcom\Exceptions\InvalidCartLineQuantityException;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 * @group headless-ecom.actions.carts
 */
class AddOrUpdatePurchasableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_add_cart_lines()
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

        $this->assertCount(0, $cart->lines);

        $action = new AddOrUpdatePurchasable;

        $action->execute($cart, $purchasable, 1);

        $this->assertCount(1, $cart->refresh()->lines);
    }

    /** @test */
    public function cannot_add_zero_quantity_line()
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

        $this->assertCount(0, $cart->lines);

        $this->expectException(InvalidCartLineQuantityException::class);

        $action = new AddOrUpdatePurchasable;

        $action->execute($cart, $purchasable, 0);
    }

    /** @test */
    public function can_update_existing_cart_line()
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

        $action = new AddOrUpdatePurchasable;

        $this->assertCount(0, $cart->lines);

        $action->execute($cart, $purchasable, 1);

        $this->assertCount(1, $cart->refresh()->lines);

        $action->execute($cart, $purchasable, 1);

        $this->assertCount(1, $cart->refresh()->lines);

        $this->assertDatabaseHas((new CartLine())->getTable(), [
            'cart_id' => $cart->id,
            'quantity' => 2,
        ]);
    }
}
