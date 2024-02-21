<?php

namespace HeadlessEcom\Database\State;

use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\Product;
use HeadlessEcom\Models\ProductType;

class ConvertProductTypeAttributesToProducts
{
    public function prepare()
    {
        //
    }

    public function run()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        if (!$this->canRun())
        {
            return;
        }

        DB::table("{$prefix}attributes")
            ->whereAttributeType(ProductType::class)
            ->update(
                [
                    'attribute_type' => Product::class,
                ]
            );

        DB::table("{$prefix}attribute_groups")
            ->whereAttributableType(ProductType::class)
            ->update(
                [
                    'attributable_type' => Product::class,
                ]
            );
    }

    protected function canRun()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return Schema::hasTable("{$prefix}attributes") &&
            Schema::hasTable("{$prefix}attribute_groups");
    }
}
