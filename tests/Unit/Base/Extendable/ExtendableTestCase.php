<?php

namespace HeadlessEcom\Tests\Unit\Base\Extendable;

use HeadlessEcom\Facades\ModelManifest;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\ProductOption;
use HeadlessEcom\Models\ProductOptionValue;
use HeadlessEcom\Tests\TestCase;

class ExtendableTestCase extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        ModelManifest::register(collect([
            Product::class => \HeadlessEcom\Tests\Stubs\Models\Product::class,
            ProductOption::class => \HeadlessEcom\Tests\Stubs\Models\ProductOption::class,
        ]));

        Product::factory()->count(20)->create();

        ProductOption::factory()
            ->has(ProductOptionValue::factory()->count(3), 'values')
            ->create([
                'name' => [
                    'en' => 'Size',
                ],
            ]);
    }
}
