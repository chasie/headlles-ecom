<?php

namespace HeadlessEcom\Validation\CartLine;

use HeadlessEcom\Validation\BaseValidator;

class CartLineQuantity extends BaseValidator
{
    /**
     * {@inheritDoc}
     */
    public function validate(): bool
    {
        $quantity = $this->parameters['quantity'] ?? 0;

        if ($quantity < 1) {
            $this->fail(
                'cart',
                __('headless-ecom::exceptions.invalid_cart_line_quantity', [
                    'quantity' => $quantity,
                ])
            );
        }

        if ($quantity > 1000000) {
            $this->fail(
                'cart',
                __('headless-ecom::exceptions.maximum_cart_line_quantity', [
                    'quantity' => 1000000,
                ])
            );
        }

        return $this->pass();
    }
}
