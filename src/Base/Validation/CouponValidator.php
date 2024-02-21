<?php

namespace HeadlessEcom\Base\Validation;

use HeadlessEcom\DiscountTypes\AmountOff;
use HeadlessEcom\DiscountTypes\BuyXGetY;
use HeadlessEcom\Models\Discount;

class CouponValidator implements CouponValidatorInterface
{
    public function validate(string $coupon): bool
    {
        return Discount::whereIn('type', [AmountOff::class, BuyXGetY::class])
            ->active()
            ->where(function ($query) {
                $query->whereNull('max_uses')
                    ->orWhereRaw('uses < max_uses');
            })->where('coupon', '=', strtoupper($coupon))->exists();
    }
}
