<?php

namespace HeadlessEcom\Base\DataTransferObjects;

use Illuminate\Support\Collection;
use HeadlessEcom\Models\Price;

class PricingResponse
{
    public function __construct(
        public Price $matched,
        public Price $base,
        public Collection $tiered,
        public Collection $customerGroupPrices,
    ) {
        //
    }
}
