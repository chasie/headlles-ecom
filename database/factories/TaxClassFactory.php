<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\TaxClass;

class TaxClassFactory extends Factory
{
    protected $model = TaxClass::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'default' => false,
        ];
    }
}
