<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\ProductAssociationFactory;

/**
 * @property int $id
 * @property int $product_parent_id
 * @property int $product_target_id
 * @property string $type
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class ProductAssociation extends BaseModel
{
    use HasFactory,
        HasMacros;

    /**
     * Define the cross sell type.
     */
    const CROSS_SELL = 'cross-sell';

    /**
     * Define the up sell type.
     */
    const UP_SELL = 'up-sell';

    /**
     * Define the alternate type.
     */
    const ALTERNATE = 'alternate';

    /**
     * Define the fillable attributes.
     *
     * @var array
     */
    protected $fillable = [
        'product_parent_id',
        'product_target_id',
        'type',
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ProductAssociationFactory
    {
        return ProductAssociationFactory::new();
    }

    /**
     * Return the parent relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_parent_id');
    }

    /**
     * Return the parent relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function target(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_target_id');
    }

    /**
     * Apply the cross sell scope.
     *
     * @return void
     */
    public function scopeCrossSell(Builder $query): Builder
    {
        $query->type(self::CROSS_SELL);
    }

    /**
     * Apply the up sell scope.
     *
     * @return void
     */
    public function scopeUpSell(Builder $query): Builder
    {
        $query->type(self::UP_SELL);
    }

    /**
     * Apply the up alternate scope.
     *
     * @return void
     */
    public function scopeAlternate(Builder $query): Builder
    {
        $query->type(self::ALTERNATE);
    }

    /**
     * Apply the type scope.
     *
     * @param  string  $type
     * @return void
     */
    public function scopeType(Builder $query, $type): Builder
    {
        return $query->whereType($type);
    }
}
