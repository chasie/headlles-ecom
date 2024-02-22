<?php

namespace HeadlessEcom\Tests\Unit\Managers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use HeadlessEcom\Facades\CartSession;
use HeadlessEcom\Managers\CartSessionManager;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartAddress;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.cart-session-manager
 */
class CartSessionManagerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_instantiate_manager()
    {
        $manager = app(CartSessionManager::class);
        $this->assertInstanceOf(CartSessionManager::class, $manager);
    }

    /**
     * @test
     */
    public function can_fetch_current_cart()
    {
        $manager = app(CartSessionManager::class);

        Currency::factory()->create([
            'default' => true,
        ]);

        Channel::factory()->create([
            'default' => true,
        ]);

        Config::set('headless-ecom.cart.auto_create', false);

        $cart = $manager->current();

        $this->assertNull($cart);

        Config::set('headless-ecom.cart.auto_create', true);

        $cart = $manager->current();

        $this->assertInstanceOf(Cart::class, $cart);

        $sessionCart = Session::get(config('headless-ecom.cart.session_key'));

        $this->assertNotNull($sessionCart);
        $this->assertEquals($cart->id, $sessionCart);
    }

    /**
     * @test
     */
    public function can_create_order_from_session_cart_and_cleanup()
    {
        Currency::factory()->create([
            'default' => true,
        ]);

        Channel::factory()->create([
            'default' => true,
        ]);

        Config::set('headless-ecom.cart.auto_create', true);

        $cart = CartSession::current();

        $shipping = CartAddress::factory()->create([
            'cart_id' => $cart->id,
            'type' => 'shipping',
        ]);

        $billing = CartAddress::factory()->create([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ]);

        $cart->setShippingAddress($shipping);
        $cart->setBillingAddress($billing);

        $sessionCart = Session::get(config('headless-ecom.cart.session_key'));

        $this->assertNotNull($sessionCart);
        $this->assertEquals($cart->id, $sessionCart);

        $order = CartSession::createOrder();

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($order->cart_id, $cart->id);

        $this->assertNull(
            Session::get(config('headless-ecom.cart.session_key'))
        );
    }

    /**
     * @test
     */
    public function can_create_order_from_session_cart_and_retain_cart()
    {
        Currency::factory()->create([
            'default' => true,
        ]);

        Channel::factory()->create([
            'default' => true,
        ]);

        Config::set('headless-ecom.cart.auto_create', true);

        $cart = CartSession::current();

        $shipping = CartAddress::factory()->create([
            'cart_id' => $cart->id,
            'type' => 'shipping',
        ]);

        $billing = CartAddress::factory()->create([
            'cart_id' => $cart->id,
            'type' => 'billing',
        ]);

        $cart->setShippingAddress($shipping);
        $cart->setBillingAddress($billing);

        $sessionCart = Session::get(config('headless-ecom.cart.session_key'));

        $this->assertNotNull($sessionCart);
        $this->assertEquals($cart->id, $sessionCart);

        $order = CartSession::createOrder(
            forget: false
        );

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals($order->cart_id, $cart->id);

        $this->assertEquals(
            $cart->id,
            Session::get(config('headless-ecom.cart.session_key'))
        );
    }

    /**
     * @test
     */
    public function canSetShippingEstimateMeta()
    {
        CartSession::estimateShippingUsing([
            'postcode' => 'NP1 1TX',
        ]);

        $meta = CartSession::getShippingEstimateMeta();
        $this->assertIsArray($meta);
        $this->assertEquals('NP1 1TX', $meta['postcode']);
    }
}
