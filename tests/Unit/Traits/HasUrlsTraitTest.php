<?php

namespace HeadlessEcom\Tests\Unit\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\Generators\UrlGenerator;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\Url;
use HeadlessEcom\Tests\TestCase;

/**
 * @group traits
 */
class HasUrlsTraitTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function urls_dont_generate_by_default()
    {
        $product = Product::factory()->create();

        $this->assertCount(0, $product->refresh()->urls);

        $this->assertDatabaseMissing((new Url)->getTable(), [
            'element_type' => Product::class,
            'element_id' => $product->id,
        ]);
    }

    /** @test * */
    public function can_generate_urls()
    {
        Language::factory()->create(['default' => true]);

        Config::set('headless-ecom.urls.generator', UrlGenerator::class);

        $product = Product::factory()->create();

        $this->assertCount(1, $product->refresh()->urls);

        $this->assertDatabaseHas((new Url)->getTable(), [
            'element_type' => Product::class,
            'element_id' => $product->id,
            'slug' => Str::slug($product->translateAttribute('name')),
        ]);
    }

    /** @test */
    public function generates_unique_urls()
    {
        Language::factory()->create(['default' => true]);

        Config::set('headless-ecom.urls.generator', UrlGenerator::class);

        $product1 = Product::factory()->create([
            'attribute_data' => collect([
                'name' => new Text('Test Product'),
            ]),
        ]);

        $product2 = Product::factory()->create([
            'attribute_data' => collect([
                'name' => new Text('Test Product'),
            ]),
        ]);

        $this->assertNotEquals(
            $product1->urls->first()->slug,
            $product2->urls->first()->slug
        );
    }
}
