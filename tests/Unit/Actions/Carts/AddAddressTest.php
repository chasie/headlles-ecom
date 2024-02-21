<?php

namespace HeadlessEcom\Tests\Unit\Actions\Carts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Carts\AddAddress;
use HeadlessEcom\Models\Address;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartAddress;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 * @group headless-ecom.actions.carts.now
 */
class AddAddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_add_address_from_addressable()
    {
        $address = Address::factory()->create();

        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $action = new AddAddress;

        $this->assertDatabaseMissing((new CartAddress)->getTable(), [
            'cart_id' => $cart->id,
        ]);

        $action->execute($cart, $address, 'billing');

        $attributes = $address->getAttributes();
        unset($attributes['shipping_default']);
        unset($attributes['billing_default']);

        $this->assertDatabaseHas((new CartAddress)->getTable(), array_merge([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ], $attributes));
    }

    /**
     * @test
     */
    public function can_add_address_from_array()
    {
        $address = Address::factory()->create();

        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $action = new AddAddress;

        $this->assertDatabaseMissing((new CartAddress)->getTable(), [
            'cart_id' => $cart->id,
        ]);

        $action->execute($cart, $address->toArray(), 'billing');

        $attributes = $address->getAttributes();
        unset($attributes['shipping_default']);
        unset($attributes['billing_default']);

        $this->assertDatabaseHas((new CartAddress)->getTable(), array_merge([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ], $attributes));
    }

    /**
     * @test
     */
    public function can_override_existing_address()
    {
        $addressA = Address::factory()->create([
            'postcode' => 'CBA 31',
        ]);

        $addressB = Address::factory()->create([
            'postcode' => 'ABC 123',
        ]);

        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $action = new AddAddress;

        $this->assertDatabaseMissing((new CartAddress)->getTable(), [
            'cart_id' => $cart->id,
        ]);

        $action->execute($cart, $addressA, 'billing');

        $attributes = $addressA->getAttributes();
        unset($attributes['shipping_default']);
        unset($attributes['billing_default']);

        $this->assertDatabaseHas((new CartAddress)->getTable(), array_merge([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ], $attributes));

        $action->execute($cart, $addressB, 'billing');

        $attributes = $addressA->getAttributes();
        unset($attributes['shipping_default']);
        unset($attributes['billing_default']);

        $this->assertDatabaseMissing((new CartAddress)->getTable(), array_merge([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ], $attributes));

        $attributes = $addressB->getAttributes();
        unset($attributes['shipping_default']);
        unset($attributes['billing_default']);

        $this->assertDatabaseHas((new CartAddress)->getTable(), array_merge([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ], $attributes));
    }
}
