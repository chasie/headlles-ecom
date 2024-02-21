<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\Models\CustomerGroup;
use HeadlessEcom\Models\TaxZone;
use HeadlessEcom\Models\TaxZoneCustomerGroup;

class TaxZoneCustomerGroupFactory extends Factory
{
    protected $model = TaxZoneCustomerGroup::class;

    public function definition(): array
    {
        return [
            'customer_group_id' => CustomerGroup::factory(),
            'tax_zone_id' => TaxZone::factory(),
        ];
    }
}
