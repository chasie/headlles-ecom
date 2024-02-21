<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Database\Factories\DiscountPurchasableFactory;
use HeadlessEcom\Discounts\Database\Factories\DiscountFactory;

/**
 * @property int $id
 * @property int $discount_id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property string $type
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class DiscountPurchasable extends BaseModel
{
    use HasFactory;

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [];

    protected $fillable = [
        'purchasable_type',
        'purchasable_id',
        'type',
    ];

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

    /**
     * Return the priceable relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeCondition(Builder $query): Builder
    {
        $query->whereType('condition');
    }
}
