<?php

namespace HeadlessEcom\Tests\Stubs\Models;

class Product extends \HeadlessEcom\Models\Product
{
    use SearchableTrait;

    /**
     * Determine if the model should be searchable.
     *
     * @return bool
     */
    public function shouldBeSearchable()
    {
        return false;
    }
}
