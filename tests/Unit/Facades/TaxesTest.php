<?php

namespace HeadlessEcom\Tests\Unit\Facades;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Base\TaxManagerInterface;
use HeadlessEcom\Base\ValueObjects\Cart\TaxBreakdown;
use HeadlessEcom\Facades\Taxes;
use HeadlessEcom\Models\Currency;
use HeadlessEcom\Models\ProductVariant;
use HeadlessEcom\Tests\Stubs\TestTaxDriver;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.taxes
 */
class TaxesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function accessor_is_correct()
    {
        $this->assertEquals(TaxManagerInterface::class, Taxes::getFacadeAccessor());
    }

    /** @test */
    public function can_extend_taxes()
    {
        Taxes::extend('testing', function ($app) {
            return $app->make(TestTaxDriver::class);
        });

        $this->assertInstanceOf(TestTaxDriver::class, Taxes::driver('testing'));

        $result = Taxes::driver('testing')->setPurchasable(
            ProductVariant::factory()->create()
        )->setCurrency(
            Currency::factory()->create()
        )->getBreakdown(123);

        $this->assertInstanceOf(TaxBreakdown::class, $result);
    }
}
