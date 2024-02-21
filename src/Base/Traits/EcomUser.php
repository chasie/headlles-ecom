<?php

namespace HeadlessEcom\Base\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use HeadlessEcom\Models\Customer;
use HeadlessEcom\Models\Order;

trait EcomUser
{
    public function customers()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this->belongsToMany(Customer::class, "{$prefix}customer_user");
    }

    public function latestCustomer()
    {
        return $this->customers()->orderBy('created_at', 'desc')->orderBy('id', 'desc')->first();
    }

    /**
     * Return the user orders relationship.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
