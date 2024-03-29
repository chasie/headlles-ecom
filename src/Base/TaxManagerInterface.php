<?php

namespace HeadlessEcom\Base;

use HeadlessEcom\Drivers\SystemTaxDriver;

interface TaxManagerInterface
{
    /**
     * Create the system driver.
     *
     * @return SystemTaxDriver
     */
    public function createSystemDriver();

    /**
     * Return the default driver reference.
     *
     * @return string
     */
    public function getDefaultDriver();

    /**
     * Build the provider.
     *
     * @return TaxDriver
     */
    public function buildProvider();
}
