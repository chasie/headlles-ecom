<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\ProductAssociationFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
     * @return BelongsTo<Product>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_parent_id');
    }

    /**
     * Return the parent relationship.
     *
     * @return BelongsTo<Product>
     */
    public function target(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_target_id');
    }

    /**
     * Apply the cross sell scope.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeCrossSell(Builder $query): Builder
    {
        return $query->type(self::CROSS_SELL);
    }

    /**
     * Apply the up sell scope.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeUpSell(Builder $query): Builder
    {
        return $query->type(self::UP_SELL);
    }

    /**
     * Apply the up alternate scope.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeAlternate(Builder $query): Builder
    {
        return $query->type(self::ALTERNATE);
    }

    /**
     * Apply the type scope.
     *
     * @param  Builder  $query
     * @param  string  $type
     * @return Builder
     */
    public function scopeType(Builder $query, string $type): Builder
    {
        return $query->whereType($type);
    }
}
