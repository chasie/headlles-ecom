<?php

namespace HeadlessEcom\Tests\Unit\Search;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\FieldTypes\TranslatedText;
use HeadlessEcom\Models\Attribute;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Search\BrandIndexer;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.search
 * @group headless-ecom.search.brand
 */
class BrandIndexerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_return_correct_searchable_data()
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
            'attribute_type' => Brand::class,
            'searchable' => true,
        ]);
        $attributeB = Attribute::factory()->create([
            'attribute_type' => Brand::class,
            'searchable' => true,
        ]);
        $attributeC = Attribute::factory()->create([
            'attribute_type' => Brand::class,
            'searchable' => false,
        ]);
        $attributeD = Attribute::factory()->create([
            'attribute_type' => Brand::class,
            'type' => TranslatedText::class,
            'searchable' => true,
        ]);

        $brand = Brand::factory()->create([
            'name' => 'Brand A',
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

        $data = app(BrandIndexer::class)->toSearchableArray($brand);

        $this->assertEquals($brand->name, $data['name']);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey($attributeA->handle, $data);
        $this->assertArrayHasKey($attributeB->handle, $data);
        $this->assertArrayNotHasKey($attributeC->handle, $data);
        $this->assertArrayHasKey($attributeD->handle.'_en', $data);
        $this->assertArrayHasKey($attributeD->handle.'_dk', $data);
    }
}
