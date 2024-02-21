<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasChannels;
use HeadlessEcom\Base\Traits\HasCustomerGroups;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Database\Factories\DiscountFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property ?string $coupon
 * @property string $type
 * @property Carbon $starts_at
 * @property Carbon $ends_at
 * @property int $uses
 * @property ?int $max_uses
 * @property int $priority
 * @property bool $stop
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Discount extends BaseModel
{
    use HasChannels,
        HasCustomerGroups,
        HasFactory,
        HasTranslations;

    protected $guarded = [];

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'data'      => 'array',
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): DiscountFactory
    {
        return DiscountFactory::new();
    }

    public function users(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                config('auth.providers.users.model'),
                "{$prefix}discount_user"
            )
            ->withTimestamps();
    }

    /**
     * Return the purchasables relationship.
     *
     * @return HasMany
     */
    public function purchasables(): HasMany
    {
        return $this->hasMany(DiscountPurchasable::class);
    }

    public function purchasableConditions(): HasMany
    {
        return $this->hasMany(DiscountPurchasable::class)->whereType('condition');
    }

    public function purchasableExclusions(): HasMany
    {
        return $this->hasMany(DiscountPurchasable::class)->whereType('exclusion');
    }

    public function purchasableLimitations(): HasMany
    {
        return $this->hasMany(DiscountPurchasable::class)->whereType('limitation');
    }

    public function purchasableRewards(): HasMany
    {
        return $this->hasMany(DiscountPurchasable::class)->whereType('reward');
    }

    public function getType()
    {
        return app($this->type)->with($this);
    }

    /**
     * Return the collections relationship.
     *
     * @return BelongsToMany
     */
    public function collections(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Collection::class,
                "{$prefix}collection_discount"
            )
            ->withPivot(['type'])->withTimestamps();
    }

    /**
     * Return the customer groups relationship.
     */
    public function customerGroups(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                CustomerGroup::class,
                "{$prefix}customer_group_discount"
            )
            ->withPivot(
                [
                    'visible',
                    'enabled',
                    'starts_at',
                    'ends_at',
                ]
            )
            ->withTimestamps();
    }

    public function brands(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Brand::class,
                "{$prefix}brand_discount"
            )
            ->withPivot(['type'])
            ->withTimestamps();
    }

    /**
     * Return the active scope.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', now())
            ->where(
                fn($query) => $query
                    ->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now())
            );
    }

    /**
     * Return the products scope.
     *
     * @param  Builder  $query
     * @param  iterable  $productIds
     * @param  string|null  $type
     * @return Builder
     */
    public function scopeProducts(
        Builder  $query,
        iterable $productIds = [],
        string   $type = null
    ): Builder {
        if (is_array($productIds))
        {
            $productIds = collect($productIds);
        }

        return $query->where(
            fn($subQuery) => $subQuery
                ->whereDoesntHave('purchasables')
                ->orWhereHas(
                    'purchasables',
                    fn($relation) => $relation
                        ->whereIn('purchasable_id', $productIds)
                        ->wherePurchasableType(Product::class)
                        ->when(
                            $type,
                            fn($query) => $query->whereType($type)
                        )
                )
        );
    }

    /**
     * Return the product variants scope.
     *
     * @param  Builder  $query
     * @param  iterable  $variantIds
     * @param  string|null  $type
     * @return Builder
     */
    public function scopeProductVariants(
        Builder  $query,
        iterable $variantIds = [],
        string   $type = null
    ): Builder {
        if (is_array($variantIds))
        {
            $variantIds = collect($variantIds);
        }

        return $query->where(
            fn($subQuery) => $subQuery
                ->whereDoesntHave('purchasables')
                ->orWhereHas(
                    'purchasables',
                    fn($relation) => $relation
                        ->whereIn('purchasable_id', $variantIds)
                        ->wherePurchasableType(ProductVariant::class)
                        ->when(
                            $type,
                            fn($query) => $query->whereType($type)
                        )
                )
        );
    }

    public function scopeUsable(Builder $query): Builder
    {
        return $query
            ->where(
                fn($subQuery) => $subQuery
                    ->whereRaw('uses < max_uses')
                    ->orWhereNull('max_uses')
            );
    }
}
