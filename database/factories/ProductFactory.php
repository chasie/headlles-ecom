<?php

namespace HeadlessEcom\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use HeadlessEcom\FieldTypes\Text;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\ProductType;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'product_type_id' => ProductType::factory(),
            'status' => 'published',
            'brand_id' => Brand::factory()->create()->id,
            'attribute_data' => collect([
                'name' => new Text($this->faker->name),
                'description' => new Text($this->faker->sentence),
            ]),
        ];
    }
}
