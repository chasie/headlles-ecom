<?php

namespace HeadlessEcom\Tests\Unit\Actions\Carts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Exceptions\Carts\CartException;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartAddress;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Tests\TestCase;
use HeadlessEcom\Validation\Cart\ValidateCartForOrderCreation;

/**
 * @group headless-ecom.actions
 * @group headless-ecom.validation.cart
 */
class ValidateCartForOrderCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_validate_missing_billing_address()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $validator = (new ValidateCartForOrderCreation)
            ->using(
            cart: $cart
        );
        $this->expectException(CartException::class);
        $this->expectExceptionMessage(__('headless-ecom::exceptions/cart.billing-missing'));

        $validator->validate();
    }

    /**
     * @test
     */
    public function can_validate_populated_billing_address()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $validator = (new ValidateCartForOrderCreation)->using(
            cart: $cart
        );

        CartAddress::factory()->create([
            'type' => 'billing',
            'cart_id' => $cart->id,
        ]);

        $this->assertTrue(
            $validator->validate()
        );
    }

    /** @test */
    public function can_validate_partial_billing_address()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $validator = (new ValidateCartForOrderCreation)->using(
            cart: $cart
        );

        CartAddress::factory()->create([
            'type' => 'billing',
            'cart_id' => $cart->id,
            'first_name' => null,
            'line_one' => null,
            'city' => null,
            'postcode' => null,
            'country_id' => null,
        ]);

        try {
            $validator->validate();
        } catch (CartException $e) {
            $errors = $e->errors();

            $this->assertTrue(
                $errors->has([
                    'country_id',
                    'first_name',
                    'line_one',
                    'city',
                    'postcode',
                ])
            );
        }
    }

    /** @test */
    public function can_validate_shippable_cart()
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create([
            'currency_id' => $currency->id,
        ]);

        $validator = (new ValidateCartForOrderCreation)->using(
            cart: $cart
        );

        CartAddress::factory()->create([
            'type' => 'billing',
            'cart_id' => $cart->id,
            'first_name' => null,
            'line_one' => null,
            'city' => null,
            'postcode' => null,
            'country_id' => null,
        ]);

        try {
            $validator->validate();
        } catch (CartException $e) {
            $errors = $e->errors();

            $this->assertTrue(
                $errors->has([
                    'country_id',
                    'first_name',
                    'line_one',
                    'city',
                    'postcode',
                ])
            );
        }
    }
}
