<?php

namespace HeadlessEcom\Tests\Unit\Actions\Carts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Carts\GenerateFingerprint;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 * @group headless-ecom.actions.carts.fingerprint
 */
class GenerateFingerprintTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_generate_cart_fingerprint()
    {
        $currency = Currency::factory()->create();
        $channel = Channel::factory()->create();

        $cart = Cart::create([
            'currency_id' => $currency->id,
            'channel_id' => $channel->id,
            'meta' => [
                'A' => 'B',
                'C' => 'D',
            ],
        ]);

        $variant = ProductVariant::factory()->create();

        Price::factory()->create([
            'price' => 100,
            'tier' => 1,
            'currency_id' => $currency->id,
            'priceable_type' => get_class($variant),
            'priceable_id' => $variant->id,
        ]);

        $cart->lines()->create([
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => $variant->id,
            'quantity' => 1,
        ]);

        $cart->coupon_code = 'valid-coupon';

        $fingerprint = (new GenerateFingerprint())->execute($cart);
        $fingerprintFromCart = $cart->fingerprint();

        $this->assertSame($fingerprint, $fingerprintFromCart);

        $cart->update([
            'meta' => [
                'C' => 'D',
                'A' => 'B',
            ],
        ]);

        $this->assertSame($fingerprintFromCart, $cart->fingerprint());

        $cart->update([
            'coupon_code' => null,
        ]);

        $this->assertNotSame($fingerprintFromCart, $cart->fingerprint());

        $cart->update([
            'meta' => null,
        ]);

        $this->assertNotSame($fingerprintFromCart, $cart->fingerprint());

        $line = $cart->lines->first();

        $line->update([
            'quantity' => 999,
        ]);

        $this->assertNotSame($fingerprintFromCart, $cart->fingerprint());
    }
}
