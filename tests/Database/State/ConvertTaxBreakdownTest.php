<?php

namespace HeadlessEcom\Tests\Database\State;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use HeadlessEcom\Database\State\ConvertTaxbreakdown;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\Channel;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Tests\TestCase;

/**
 * @group database.state
 */
class ConvertTaxBreakdownTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_run()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        Storage::fake('local');

        Language::factory()->create([
            'default' => true,
        ]);

        $channel = Channel::factory()->create([
            'default' => true,
        ]);

        Currency::factory()->create([
            'code' => 'GBP',
        ]);

        DB::table("{$prefix}orders")->insert([
            'channel_id' => $channel->id,
            'new_customer' => false,
            'user_id' => null,
            'status' => 'awaiting-payment',
            'reference' => 123123,
            'sub_total' => 400,
            'discount_total' => 0,
            'shipping_total' => 0,
            'tax_breakdown' => '[{"total": 333, "identifier": "tax_rate_1", "percentage": 20, "description": "VAT"}]',
            'tax_total' => 200,
            'total' => 100,
            'notes' => null,
            'currency_code' => 'GBP',
            'compare_currency_code' => 'GBP',
            'exchange_rate' => 1,
            'meta' => '[]',
        ]);

        (new ConvertTaxbreakdown)->run();

        $this->assertDatabaseHas("{$prefix}orders", [
            'tax_breakdown' => '[{"description":"VAT","identifier":"tax_rate_1","percentage":20,"value":333,"currency_code":"GBP"}]',
        ]);

    }
}
