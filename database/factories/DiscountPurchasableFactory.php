<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\DiscountPurchasable;
use HeadlessEcom\Models\ProductVariant;

class DiscountPurchasableFactory extends Factory
{
    protected $model = DiscountPurchasable::class;

    public function definition(): array
    {
        return [
            'purchasable_id' => ProductVariant::factory(),
            'purchasable_type' => ProductVariant::class,
        ];
    }
}
