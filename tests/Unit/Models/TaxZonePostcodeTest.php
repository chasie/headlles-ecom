<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Country;
use HeadlessEcom\Models\TaxZone;
use HeadlessEcom\Models\TaxZonePostcode;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.models
 */
class TaxZonePostcodeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_tax_zone_postcode()
    {
        $data = [
            'tax_zone_id' => TaxZone::factory()->create()->id,
            'country_id' => Country::factory()->create()->id,
            'postcode' => 123456,
        ];

        TaxZonePostcode::factory()->create($data);

        $this->assertDatabaseHas((new TaxZonePostcode())->getTable(), $data);
    }
}
