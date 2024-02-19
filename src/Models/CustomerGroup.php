<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasDefaultRecord;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\CustomerGroupFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property bool $default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class CustomerGroup extends BaseModel
{
    use HasDefaultRecord,
        HasFactory,
        HasMacros;

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CustomerGroupFactory
    {
        return CustomerGroupFactory::new();
    }

    /**
     * Return the customer's relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Customer::class,
                "{$prefix}customer_customer_group"
            )
            ->withTimestamps();
    }
}
