<?php

namespace HeadlessEcom\PaymentTypes;

use HeadlessEcom\Base\PaymentTypeInterface;
use HeadlessEcom\Models\Cart;
use HeadlessEcom\Models\Order;

abstract class AbstractPayment implements PaymentTypeInterface
{
    /**
     * The instance of the cart.
     *
     * @var \HeadlessEcom\Models\Cart
     */
    protected ?Cart $cart = null;

    /**
     * The instance of the order.
     *
     * @var \HeadlessEcom\Models\Order
     */
    protected ?Order $order = null;

    /**
     * Any config for this payment provider.
     */
    protected array $config = [];

    /**
     * Data storage.
     */
    protected array $data = [];

    /**
     * {@inheritDoc}
     */
    public function cart(Cart $cart): self
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function order(Order $order): self
    {
        $this->order = $order;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function withData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
