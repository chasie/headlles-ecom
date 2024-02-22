<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\Url;
use HeadlessEcom\Tests\TestCase;

/**
 * @group models
 * @group urls
 */
class UrlTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_a_url()
    {
        $product = Product::factory()->create();
        $language = Language::factory()->create();

        $data = [
            'language_id' => $language->id,
            'element_id' => $product->id,
            'element_type' => Product::class,
            'slug' => Str::slug($product->translateAttribute('name')),
            'default' => true,
        ];

        Url::create($data);

        $prefix = config('headless-ecom.database.table_prefix');
        $this->assertDatabaseHas("{$prefix}urls", $data);
    }

    /** @test */
    public function can_fetch_element_from_url_relationship()
    {
        $product = Product::factory()->create();
        $language = Language::factory()->create();

        $data = [
            'language_id' => $language->id,
            'element_id' => $product->id,
            'element_type' => Product::class,
            'slug' => Str::slug($product->translateAttribute('name')),
            'default' => true,
        ];

        $url = Url::create($data);

        $this->assertInstanceOf(Product::class, $url->element);
        $this->assertEquals($product->id, $url->element->id);
    }
}
