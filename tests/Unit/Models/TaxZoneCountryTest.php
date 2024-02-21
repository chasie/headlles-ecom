<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Country;
use HeadlessEcom\Models\TaxZone;
use HeadlessEcom\Models\TaxZoneCountry;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.models
 */
class TaxZoneCountryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_tax_zone_country()
    {
        $data = [
            'tax_zone_id' => TaxZone::factory()->create()->id,
            'country_id' => Country::factory()->create()->id,
        ];

        TaxZoneCountry::factory()->create($data);

        $this->assertDatabaseHas((new TaxZoneCountry())->getTable(), $data);
    }
}
