<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Database\Factories\DiscountPurchasableFactory;
use Chasie\HeadlesEcom\Discounts\Database\Factories\DiscountFactory;

class DiscountCollection extends BaseModel
{
    use HasFactory;

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [];

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     *
     * @return DiscountFactory
     */
    protected static function newFactory(): DiscountPurchasableFactory
    {
        return DiscountPurchasableFactory::new();
    }

    /**
     * Return the discount relationship.
     *
     * @return BelongsTo
     */
    public function discount(): BelongsTo
    {
        return $this->belongsTo(Discount::class);
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
