<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\Models\Attribute;
use HeadlessEcom\Models\AttributeGroup;
use HeadlessEcom\Tests\TestCase;

class AttributeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_attribute()
    {
        $options = [
            'Red',
            'Blue',
            'Green',
        ];

        $attribute = Attribute::factory()
            ->for(AttributeGroup::factory())
            ->create([
                'position' => 4,
                'name' => [
                    'en' => 'Meta Description',
                ],
                'handle' => 'meta_description',
                'section' => 'product_variant',
                'type' => \HeadlessEcom\FieldTypes\Text::class,
                'required' => false,
                'default_value' => '',
                'configuration' => [
                    'options' => $options,
                ],
                'system' => true,
            ]);

        $this->assertEquals('Meta Description', $attribute->name->get('en'));
        $this->assertEquals('meta_description', $attribute->handle);
        $this->assertEquals(\HeadlessEcom\FieldTypes\Text::class, $attribute->type);
        $this->assertTrue($attribute->system);
        $this->assertEquals(4, $attribute->position);
        $this->assertEquals($options, $attribute->configuration->get('options'));
    }
}
