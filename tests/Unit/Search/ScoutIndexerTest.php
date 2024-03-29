<?php

namespace HeadlessEcom\Tests\Unit\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\FieldTypes\TranslatedText;
use HeadlessEcom\Models\Attribute;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Search\ScoutIndexer;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.search
 */
class ScoutIndexerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_get_correct_index_name()
    {
        Config::set('scout.prefix', 'ecom_');

        $product = Product::factory()->create();
        $collection = Collection::factory()->create();

        $productIndex = app(ScoutIndexer::class)->searchableAs($product);
        $collectionIndex = app(ScoutIndexer::class)->searchableAs($collection);

        $this->assertEquals('ecom_products', $productIndex);
        $this->assertEquals('ecom_collections', $collectionIndex);
    }

    /** @test */
    public function searchable_is_enabled_by_default()
    {
        $product = Product::factory()->create();
        $collection = Collection::factory()->create();

        $this->assertTrue(
            app(ScoutIndexer::class)->shouldBeSearchable($product)
        );
        $this->assertTrue(
            app(ScoutIndexer::class)->shouldBeSearchable($collection)
        );
    }

    /** @test */
    public function can_return_searchable_array()
    {
        $product = Product::factory()->create();

        $data = app(ScoutIndexer::class)->toSearchableArray($product);

        $this->assertSame([
            'id' => $product->id,
        ], $data);
    }

    /** @test */
    public function includes_searchable_attributes_in_searchable_array()
    {
        Language::factory()->create([
            'code' => 'en',
            'default' => true,
        ]);

        Language::factory()->create([
            'code' => 'dk',
            'default' => false,
        ]);

        $attributeA = Attribute::factory()->create([
            'attribute_type' => Product::class,
            'searchable' => true,
        ]);
        $attributeB = Attribute::factory()->create([
            'attribute_type' => Product::class,
            'searchable' => true,
        ]);
        $attributeC = Attribute::factory()->create([
            'attribute_type' => Product::class,
            'searchable' => false,
        ]);
        $attributeD = Attribute::factory()->create([
            'attribute_type' => Product::class,
            'type' => TranslatedText::class,
            'searchable' => true,
        ]);

        $product = Product::factory()->create([
            'attribute_data' => collect([
                $attributeA->handle => new Text('Attribute A'),
                $attributeB->handle => new Text('Attribute B'),
                $attributeC->handle => new Text('Attribute C'),
                $attributeD->handle => new TranslatedText([
                    'en' => 'Attribute D EN',
                    'dk' => 'Attribute D DK',
                ]),
            ]),
        ]);

        $data = app(ScoutIndexer::class)->toSearchableArray($product);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey($attributeA->handle, $data);
        $this->assertArrayHasKey($attributeB->handle, $data);
        $this->assertArrayNotHasKey($attributeC->handle, $data);
        $this->assertArrayHasKey($attributeD->handle.'_en', $data);
        $this->assertArrayHasKey($attributeD->handle.'_dk', $data);
    }
}
