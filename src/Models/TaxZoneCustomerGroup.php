<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\TaxZoneCustomerGroupFactory;

/**
 * @property int $id
 * @property ?int $tax_zone_id
 * @property ?int $customer_group_id
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class TaxZoneCustomerGroup extends BaseModel
{
    use HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TaxZoneCustomerGroupFactory
    {
        return TaxZoneCustomerGroupFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return the customer group relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Return the tax zone relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function taxZone(): BelongsTo
    {
        return $this->belongsTo(TaxZone::class);
    }
}
