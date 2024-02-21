<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\CollectionGroup;

class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        return [
            'collection_group_id' => CollectionGroup::factory(),
            'attribute_data' => collect([
                'name' => new Text($this->faker->name),
            ]),
        ];
    }
}
