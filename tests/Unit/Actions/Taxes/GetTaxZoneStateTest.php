<?php

namespace HeadlessEcom\Tests\Unit\Actions\Taxes;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Actions\Taxes\GetTaxZoneState;
use HeadlessEcom\Models\State;
use HeadlessEcom\Models\TaxZoneState;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 */
class GetTaxZoneStateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_match_exact_state_name()
    {
        $california = State::factory()->create([
            'code' => 'CA',
            'name' => 'California',
        ]);

        $alabama = State::factory()->create([
            'code' => 'AL',
            'name' => 'Alabama',
        ]);

        TaxZoneState::factory()->create([
            'state_id' => $california->id,
        ]);

        $al = TaxZoneState::factory()->create([
            'state_id' => $alabama->id,
        ]);

        $zone = app(GetTaxZoneState::class)->execute('Alabama');

        $this->assertEquals($al->id, $zone->id);
    }

    /** @test */
    public function can_match_exact_state_code()
    {
        $california = State::factory()->create([
            'code' => 'CA',
            'name' => 'California',
        ]);

        $alabama = State::factory()->create([
            'code' => 'AL',
            'name' => 'Alabama',
        ]);

        TaxZoneState::factory()->create([
            'state_id' => $california->id,
        ]);

        $al = TaxZoneState::factory()->create([
            'state_id' => $alabama->id,
        ]);

        $zone = app(GetTaxZoneState::class)->execute('AL');

        $this->assertNotNull($zone);

        $this->assertEquals($al->id, $zone?->id);
    }

    /** @test */
    public function can_mismatch_exact_state_name()
    {
        $california = State::factory()->create([
            'code' => 'CA',
            'name' => 'California',
        ]);

        $alabama = State::factory()->create([
            'code' => 'AL',
            'name' => 'Alabama',
        ]);

        TaxZoneState::factory()->create([
            'state_id' => $california->id,
        ]);

        $al = TaxZoneState::factory()->create([
            'state_id' => $alabama->id,
        ]);

        $zone = app(GetTaxZoneState::class)->execute('Alaba');

        $this->assertNull($zone);

        $this->assertNotEquals($al->id, $zone?->id);
    }
}
