<?php

namespace HeadlessEcom\Rules;

use Illuminate\Contracts\Validation\Rule;
use HeadlessEcom\Base\Validation\CouponValidator;

class ValidCoupon implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return app(
            config('headless-ecom.discounts.coupon_validator', CouponValidator::class)
        )->validate($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is not valid or has been used too many times';
    }
}
