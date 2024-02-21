<?php

namespace HeadlessEcom\Actions\Collections;

use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\Currency;

class SortProducts
{
    /**
     * Execute the action.
     *
     * @return mixed
     */
    public function execute(Collection $collection): mixed
    {
        [$sort, $direction] = explode(':', $collection->sort);

        switch ($sort)
        {
            case 'min_price':
                $products = app(SortProductsByPrice::class)
                    ->execute(
                        $collection->products,
                        Currency::getDefault(),
                        $direction
                    );
                break;
            case 'sku':
                $products = app(SortProductsBySku::class)
                    ->execute(
                        $collection->products,
                        $direction
                    );
                break;
            default:
                $products = $collection->products;
                break;
        }

        return $products;
    }
}
