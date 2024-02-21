<?php

namespace HeadlessEcom\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_make_a_collection()
    {
        $collection = Collection::factory()
            ->create([
                'attribute_data' => collect([
                    'name' => new Text('Red Products'),
                ]),
            ]);

        $this->assertEquals($collection->translateAttribute('name'), 'Red Products');
    }
}
