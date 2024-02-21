<?php

namespace HeadlessEcom\Tests\Unit\Facades;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Base\DataTransferObjects\PaymentAuthorize;
use HeadlessEcom\Base\PaymentManagerInterface;
use HeadlessEcom\Facades\Payments;
use HeadlessEcom\Tests\Stubs\TestPaymentDriver;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.payments
 */
class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function accessor_is_correct()
    {
        $this->assertEquals(PaymentManagerInterface::class, Payments::getFacadeAccessor());
    }

    /** @test */
    public function can_extend_payments()
    {
        Payments::extend('testing', function ($app) {
            return $app->make(TestPaymentDriver::class);
        });

        $this->assertInstanceOf(TestPaymentDriver::class, Payments::driver('testing'));

        $result = Payments::driver('testing')->authorize();

        $this->assertInstanceOf(PaymentAuthorize::class, $result);
    }
}
