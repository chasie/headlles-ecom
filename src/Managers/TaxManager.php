<?php

namespace HeadlessEcom\Managers;

use Illuminate\Support\Manager;
use HeadlessEcom\Drivers\SystemTaxDriver;

class TaxManager extends Manager
{
    public function createSystemDriver()
    {
        return $this->buildProvider(SystemTaxDriver::class);
    }

    /**
     * Build a tax provider instance.
     *
     * @param  string  $provider
     * @return mixed
     */
    public function buildProvider($provider)
    {
        return $this->container->make($provider);
    }

    public function getDefaultDriver()
    {
        return config('headless-ecom.taxes.driver', 'system');
    }
}
