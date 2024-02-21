<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\Country;
use HeadlessEcom\Models\TaxZone;
use HeadlessEcom\Models\TaxZonePostcode;

class TaxZonePostcodeFactory extends Factory
{
    protected $model = TaxZonePostcode::class;

    public function definition(): array
    {
        return [
            'tax_zone_id' => TaxZone::factory(),
            'country_id' => Country::factory(),
            'postcode' => $this->faker->postcode,
        ];
    }
}
