<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Attribute;
use HeadlessEcom\Models\AttributeGroup;
use HeadlessEcom\Models\ProductType;
use HeadlessEcom\Tests\TestCase;

class ProductTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_product_type()
    {
        $productType = ProductType::factory()
            ->has(
                Attribute::factory()->for(AttributeGroup::factory())->count(1),
                'mappedAttributes',
            )
            ->create([
                'name' => 'Bob',
            ]);

        $this->assertEquals('Bob', $productType->name);
    }
}
