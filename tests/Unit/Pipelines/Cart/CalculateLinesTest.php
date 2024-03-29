<?php

namespace HeadlessEcom\Tests\Unit\Pipelines\Cart;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Price;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Pipelines\Cart\CalculateLines;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.carts.pipelines
 */
class CalculateLinesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     * @dataProvider providePurchasableData
     */
    public function can_calculate_lines($expectedUnitPrice, $incomingUnitPrice, $unitQuantity)
    {
        $currency = Currency::factory()->create();

        $cart = Cart::factory()->create(
            [
                'currency_id' => $currency->id,
            ]
        );

        $purchasable = ProductVariant::factory()->create(
            [
                'unit_quantity' => $unitQuantity,
            ]
        );

        Price::factory()->create(
            [
                'price'          => $incomingUnitPrice,
                'tier'           => 1,
                'currency_id'    => $currency->id,
                'priceable_type' => get_class($purchasable),
                'priceable_id'   => $purchasable->id,
            ]
        );

        $cart->lines()->create(
            [
                'purchasable_type' => get_class($purchasable),
                'purchasable_id'   => $purchasable->id,
                'quantity'         => 1,
            ]
        );


        $cart = app(CalculateLines::class)
            ->handle(
                $cart, function ($cart)
            {
                return $cart;
            }
            );

        $cartLine = $cart->lines->first();

        $this->assertEquals($cartLine->subTotal->unitDecimal, $expectedUnitPrice);
    }

    public static function providePurchasableData(): array
    {
        return [
            'purchasable with 1 unit quantity'           => [
                '1.00',
                '100',
                '1',
            ],
            'purchasable with 10 unit quantity'          => [
                '0.10',
                '100',
                '10',
            ],
            'purchasable with 100 unit quantity'         => [
                '0.01',
                '100',
                '100',
            ],
            'another purchasable with 100 unit quantity' => [
                '0.55',
                '5503',
                '100',
            ]
        ];
    }
}
