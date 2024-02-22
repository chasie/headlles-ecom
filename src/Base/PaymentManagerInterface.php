<?php

namespace HeadlessEcom\Base;

interface PaymentManagerInterface
{
    /**
     * Return the default driver reference.
     *
     * @return string
     */
    public function getDefaultDriver(): string;

    /**
     * Build the provider.
     *
     * @return TaxDriver
     */
    public function buildProvider(): TaxDriver;
}
