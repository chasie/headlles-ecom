<?php

use HeadlessEcom\Base\Traits\EcomUser;

if (! function_exists('is_HeadlessEcom_user')) {
    function is_HeadlessEcom_user($user)
    {
        $traits = class_uses_recursive($user);

        return in_array(EcomUser::class, $traits);
    }
}

if (! function_exists('prices_inc_tax')) {
    function prices_inc_tax()
    {
        return config('headless-ecom.pricing.stored_inclusive_of_tax', false);
    }
}
