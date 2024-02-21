<?php

namespace HeadlessEcom\Tests\Unit\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\ProductOption;
use HeadlessEcom\Search\ProductOptionIndexer;
use HeadlessEcom\Tests\TestCase;

/**
 * @group lunar.search
 * @group lunar.search.product_option
 */
class ProductOptionIndexerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_return_correct_searchable_data()
    {
        $productOption = ProductOption::factory()->create();

        $data = app(ProductOptionIndexer::class)->toSearchableArray($productOption);

        $this->assertEquals($productOption->name->en, $data['name_en']);
        $this->assertEquals($productOption->label->en, $data['label_en']);
    }
}
