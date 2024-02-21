<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Discount;
use HeadlessEcom\Tests\TestCase;

/**
 * @group models.discounts
 * @group urls
 */
class DiscountTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_apply_usable_scope()
    {
        Discount::factory()->create([
            'max_uses' => null,
        ]);

        Discount::factory()->create([
            'uses' => 10,
            'max_uses' => 11,
        ]);

        $discountC = Discount::factory()->create([
            'uses' => 10,
            'max_uses' => 10,
        ]);

        $discounts = Discount::usable()->get();

        $this->assertCount(2, $discounts);
        $this->assertNull($discounts->first(
            fn ($discount) => $discount->id == $discountC->id
        ));
    }
}
