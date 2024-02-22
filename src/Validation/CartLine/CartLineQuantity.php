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
                __('headless-ecom::exceptions/cart.cart_line_quantity-invalid', [
                    'quantity' => $quantity,
                ])
            );
        }

        if ($quantity > 1000000) {
            $this->fail(
                'cart',
                __('headless-ecom::exceptions/cart.cart_line_quantity-maximum', [
                    'quantity' => 1000000,
                ])
            );
        }

        return $this->pass();
    }
}
