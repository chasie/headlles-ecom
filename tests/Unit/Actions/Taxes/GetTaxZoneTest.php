<?php

namespace HeadlessEcom\Tests\Unit\Actions\Taxes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Taxes\GetTaxZone;
use HeadlessEcom\Actions\Taxes\GetTaxZoneCountry;
use HeadlessEcom\Models\Address;
use HeadlessEcom\Models\Country;
use HeadlessEcom\Models\State;
use HeadlessEcom\Models\TaxZone;
use HeadlessEcom\Models\TaxZoneCountry;
use HeadlessEcom\Models\TaxZonePostcode;
use HeadlessEcom\Models\TaxZoneState;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.actions
 */
class GetTaxZoneTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_prioritize_taxzones()
    {
        $postcode = 'SW1A 0AA';

        $state = State::factory()->create([
            'code' => 'AL',
            'name' => 'Alabama',
        ]);

        $country = Country::factory()->create([
            'name' => 'Belgium',
        ]);

        $taxZonePostcode = TaxZonePostcode::factory()->create([
            'tax_zone_id' => TaxZone::factory(['default' => false]),
            'postcode' => $postcode,
        ]);

        $taxZoneState = TaxZoneState::factory()->create([
            'tax_zone_id' => TaxZone::factory(['default' => false]),
            'state_id' => $state->id,
        ]);

        $taxZoneCountry = TaxZoneCountry::factory()->create([
            'tax_zone_id' => TaxZone::factory(['default' => false]),
            'country_id' => $country->id,
        ]);

        $defaultTaxZone = TaxZone::factory(['default' => true])->create();

        // postcode, state and country match => postcode tax zone should be returned
        $addressWithAllMatching = Address::factory()->create([
            'postcode' => $postcode,
            'state' => $state->name,
            'country_id' => $country->id,
        ]);

        $zone1 = app(GetTaxZone::class)->execute($addressWithAllMatching);

        $this->assertEquals($taxZonePostcode->tax_zone_id, $zone1->id);

        // only state and country match => state tax zone should be returned
        $addressWithOnlyStateAndCountryMatching = Address::factory()->create([
            'postcode' => '1234AB',
            'state' => $state->name,
            'country_id' => $country->id,
        ]);

        $zone2 = app(GetTaxZone::class)->execute($addressWithOnlyStateAndCountryMatching);

        $this->assertEquals($taxZoneState->tax_zone_id, $zone2->id);

        // only country matches => country tax zone should be returned
        $addressWithOnlyCountryMatching = Address::factory()->create([
            'postcode' => '1234AB',
            'state' => 'Alaska',
            'country_id' => $country->id,
        ]);

        $zone3 = app(GetTaxZone::class)->execute($addressWithOnlyCountryMatching);

        $this->assertEquals($taxZoneCountry->tax_zone_id, $zone3->id);

        // nothing matches => default tax zone should be returned
        $addressWithOnlyCountryMatching = Address::factory()->create([
            'postcode' => '1234AB',
            'state' => 'Alaska',
            'country_id' => 123,
        ]);

        $zone3 = app(GetTaxZone::class)->execute($addressWithOnlyCountryMatching);

        $this->assertEquals($defaultTaxZone->id, $zone3->id);
    }
}
