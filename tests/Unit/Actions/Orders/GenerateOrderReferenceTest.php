<?php

namespace HeadlessEcom\Tests\Unit\Actions\Orders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use HeadlessEcom\Actions\Orders\GenerateOrderReference;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Order;
use HeadlessEcom\Tests\Stubs\TestOrderReferenceGenerator;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.actions
 */
class GenerateOrderReferenceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Language::factory()->create([
            'default' => true,
            'code' => 'en',
        ]);

        Currency::factory()->create([
            'default' => true,
            'decimal_places' => 2,
        ]);
    }

    /** @test */
    public function can_generate_reference()
    {
        $order = Order::factory()->create([
            'reference' => null,
            'placed_at' => now(),
        ]);

        $this->assertNull($order->reference);

        $result = app(GenerateOrderReference::class)->execute($order);

        $this->assertEquals($order->created_at->format('Y-m').'-0001', $result);
    }

    /** @test */
    public function can_override_generator_via_config()
    {
        $order = Order::factory()->create([
            'reference' => null,
            'placed_at' => now(),
        ]);

        Config::set('headless-ecom.orders.reference_generator', TestOrderReferenceGenerator::class);

        $this->assertNull($order->reference);

        $result = app(GenerateOrderReference::class)->execute($order);

        $this->assertEquals('reference-'.$order->id, $result);
    }

    /** @test */
    public function can_set_generator_to_null()
    {
        $order = Order::factory()->create([
            'reference' => null,
            'placed_at' => now(),
        ]);

        Config::set('headless-ecom.orders.reference_generator', null);

        $this->assertNull($order->reference);

        $result = app(GenerateOrderReference::class)->execute($order);

        $this->assertNull($result);
    }
}
