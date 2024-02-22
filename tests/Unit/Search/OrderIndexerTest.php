<?php

namespace HeadlessEcom\Tests\Unit\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Search\OrderIndexer;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.search
 * @group headless-ecom.search.order
 */
class OrderIndexerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_return_correct_searchable_data()
    {
        Currency::factory()->create([
            'code' => 'GBP',
            'default' => true,
        ]);

        $order = Order::factory()->create([
            'user_id' => null,
            'placed_at' => now(),
            'meta' => [
                'foo' => 'bar',
            ]
        ]);

        $data = app(OrderIndexer::class)->toSearchableArray($order);

        $this->assertEquals('GBP', $data['currency_code']);
        $this->assertEquals($order->channel->name, $data['channel']);
        $this->assertEquals($order->total->value, $data['total']);
    }
}
