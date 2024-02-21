<?php

namespace HeadlessEcom\Base\ValueObjects\Cart;

use HeadlessEcom\Models\ProductVariant;

class FreeItem
{
    /**
     * The associated product variant.
     */
    public ProductVariant $productVariant;

    /**
     * Quantity available from promotions,
     */
    public int $available = 1;

    /**
     * Group for the item.
     * Allows free items to be grouped together, e.g. by promotion.
     */
    public string $group = '';
}
