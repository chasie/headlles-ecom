<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\TaxClass;
use HeadlessEcom\Models\TaxRate;
use HeadlessEcom\Models\TaxRateAmount;

class TaxRateAmountFactory extends Factory
{
    protected $model = TaxRateAmount::class;

    public function definition(): array
    {
        return [
            'tax_rate_id' => TaxRate::factory(),
            'tax_class_id' => TaxClass::factory(),
            'percentage' => 20,
        ];
    }
}
