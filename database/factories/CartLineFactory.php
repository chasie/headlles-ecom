<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\CartLine;
use HeadlessEcom\Models\ProductVariant;

class CartLineFactory extends Factory
{
    protected $model = CartLine::class;

    public function definition(): array
    {
        return [
            'cart_id' => Cart::factory(),
            'quantity' => $this->faker->numberBetween(0, 1000),
            'purchasable_type' => ProductVariant::class,
            'purchasable_id' => ProductVariant::factory(),
            'meta' => null,
        ];
    }
}
