<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use HeadlessEcom\Generators\UrlGenerator;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Language;
use HeadlessEcom\Models\Url;
use HeadlessEcom\Tests\TestCase;

/**
 * @group headless-ecom.brands
 */
class BrandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_brand()
    {
        $brand = Brand::factory()->create([
            'name' => 'Test Brand',
        ]);
        $this->assertEquals('Test Brand', $brand->name);
    }

    /** @test */
    public function can_generate_url()
    {
        Config::set('headless-ecom.urls.generator', UrlGenerator::class);

        Language::factory()->create([
            'default' => true,
        ]);

        $brand = Brand::factory()->create([
            'name' => 'Test Brand',
        ]);

        $this->assertDatabaseHas((new Url)->getTable(), [
            'slug' => 'test-brand',
            'element_type' => Brand::class,
            'element_id' => $brand->id,
        ]);
    }

    /** @test */
    public function generates_unique_urls()
    {
        Config::set('headless-ecom.urls.generator', UrlGenerator::class);

        Language::factory()->create([
            'default' => true,
        ]);

        $brand1 = Brand::factory()->create([
            'name' => 'Test Brand',
        ]);

        $brand2 = Brand::factory()->create([
            'name' => 'Test Brand',
        ]);

        $brand3 = Brand::factory()->create([
            'name' => 'Test Brand',
        ]);

        $brand4 = Brand::factory()->create([
            'name' => 'Brand Test',
        ]);

        $this->assertEquals(
            'test-brand',
            $brand1->urls->first()->slug
        );

        $this->assertEquals(
            'test-brand-2',
            $brand2->urls->first()->slug
        );

        $this->assertEquals(
            'test-brand-3',
            $brand3->urls->first()->slug
        );

        $this->assertEquals(
            'brand-test',
            $brand4->urls->first()->slug
        );
    }
}
