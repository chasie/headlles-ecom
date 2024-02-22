<?php

namespace HeadlessEcom\Tests\Database\State;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\ProductType;
use HeadlessEcom\Tests\TestCase;

/**
 * @group database.state
 */
class EnsureBrandsAreUpgradedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_run()
    {
        Storage::fake('local');

        Language::factory()->create(
            [
                'default' => true,
            ]
        );
        $productType = ProductType::factory()->create();

        $brandA = Brand::forceCreate(
            [
                'name'           => 'Brand A',
                'attribute_data' => collect(
                    [
                        'name' => new Text('Brand A'),
                    ]
                ),
            ]
        );
        $brandB = Brand::forceCreate(
            [
                'name'           => 'Brand B',
                'attribute_data' => collect(
                    [
                        'name' => new Text('Brand B'),
                    ]
                ),
            ]
        );

        $productA = Product::forceCreate(
            [
                'brand_id'        => $brandA->id,
                'product_type_id' => $productType->id,
                'status'          => 'published',
                'attribute_data'  => collect(
                    [
                        'name' => new Text('Product A'),
                    ]
                ),
            ]
        );

        $productB = Product::forceCreate(
            [
                'brand_id'        => $brandA->id,
                'product_type_id' => $productType->id,
                'status'          => 'published',
                'attribute_data'  => collect(
                    [
                        'name' => new Text('Product B'),
                    ]
                ),
            ]
        );

        $productC = Product::forceCreate(
            [
                'brand_id'        => $brandB->id,
                'product_type_id' => $productType->id,
                'status'          => 'published',
                'attribute_data'  => collect(
                    [
                        'name' => new Text('Product C'),
                    ]
                ),
            ]
        );

        $this->assertDatabaseHas(
            (new Brand)->getTable(),
            [
                'name' => 'Brand A',
            ]
        );

        $this->assertDatabaseHas(
            (new Brand)->getTable(),
            [
                'name' => 'Brand B',
            ]
        );

        $this->assertCount(
            2,
            Product::whereBrandId($brandA->id)->get()
        );
        $this->assertCount(
            1,
            Product::whereBrandId($brandB->id)->get()
        );
    }
}
