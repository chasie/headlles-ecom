<?php

namespace HeadlessEcom\Tests\Unit\Console;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\DataTypes\Price as DataTypesPrice;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.traits.cache
 */
class CachesPropertiesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_cache_model_properties()
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

        $cart = $cart->calculate();

        $this->assertInstanceOf(DataTypesPrice::class, $cart->subTotal);
        $this->assertEquals(100, $cart->subTotal->value);
        $this->assertInstanceOf(DataTypesPrice::class, $cart->total);
        $this->assertEquals(120, $cart->total->value);
        $this->assertInstanceOf(DataTypesPrice::class, $cart->taxTotal);
        $this->assertEquals(20, $cart->taxTotal->value);

        // When now fetching from the database it should automatically be hydrated...
        $cart = Cart::find($cart->id);

        $this->assertInstanceOf(DataTypesPrice::class, $cart->subTotal);
        $this->assertEquals(100, $cart->subTotal->value);
        $this->assertInstanceOf(DataTypesPrice::class, $cart->total);
        $this->assertEquals(120, $cart->total->value);
        $this->assertInstanceOf(DataTypesPrice::class, $cart->taxTotal);
        $this->assertEquals(20, $cart->taxTotal->value);
    }
}
