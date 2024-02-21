<?php

namespace HeadlessEcom\Tests\Stubs\Models;

class ProductSwapModel extends \HeadlessEcom\Models\Product
{
    public function shouldBeSearchable()
    {
        return false;
    }
}
