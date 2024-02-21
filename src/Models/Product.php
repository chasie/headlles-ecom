<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\AsAttributeData;
use HeadlessEcom\Base\Traits\HasChannels;
use HeadlessEcom\Base\Traits\HasCustomerGroups;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasMedia;
use HeadlessEcom\Base\Traits\HasTags;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Base\Traits\HasUrls;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Base\Traits\Searchable;
use HeadlessEcom\Database\Factories\ProductFactory;
use HeadlessEcom\Jobs\Products\Associations\Associate;
use HeadlessEcom\Jobs\Products\Associations\Dissociate;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property ?int $brand_id
 * @property int $product_type_id
 * @property string $status
 * @property array $attribute_data
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Product extends BaseModel implements SpatieHasMedia
{
    use HasChannels,
        HasCustomerGroups,
        HasFactory,
        HasMacros,
        HasMedia,
        HasTags,
        HasTranslations,
        HasUrls,
        LogsActivity,
        Searchable,
        SoftDeletes;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ProductFactory
    {
        return ProductFactory::new();
    }

    /**
     * Define which attributes should be
     * fillable during mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'attribute_data',
        'product_type_id',
        'status',
        'brand_id',
    ];

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
    ];

    /**
     * Returns the attributes to be stored against this model.
     *
     * @return array
     */
    public function mappedAttributes(): array
    {
        return $this->productType->mappedAttributes;
    }

    /**
     * Return the product type relation.
     *
     * @return BelongsTo<ProductType>
     */
    public function productType(): BelongsTo
    {
        return $this->belongsTo(ProductType::class);
    }

    /**
     * Return the product images relation.
     *
     * @return MorphMany
     */
    public function images(): MorphMany
    {
        return $this->media()->where('collection_name', 'images');
    }

    /**
     * Return the product variants relation.
     *
     * @return HasMany<ProductVariant>
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Return the product collections relation.
     *
     * @return BelongsToMany<Collection>
     */
    public function collections(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Collection::class,
                "{$prefix}collection_product"
            )
            ->withPivot(
                [
                    'position'
                ]
            )
            ->withTimestamps();
    }

    /**
     * Return the associations relationship.
     *
     * @return HasMany<ProductAssociation>
     */
    public function associations(): HasMany
    {
        return $this->hasMany(ProductAssociation::class, 'product_parent_id');
    }

    /**
     * Return the associations relationship.
     *
     * @return HasMany<ProductAssociation>
     */
    public function inverseAssociations(): HasMany
    {
        return $this->hasMany(ProductAssociation::class, 'product_target_id');
    }

    /**
     * Associate a product to another with a type.
     *
     * @param  mixed  $product
     * @param  string  $type
     * @return void
     */
    public function associate(mixed $product, string $type): void
    {
        Associate::dispatch($this, $product, $type);
    }

    /**
     * Dissociate a product to another with a type.
     *
     * @param  mixed  $product
     * @param  null|string  $type
     * @return void
     */
    public function dissociate(mixed $product, ?string $type = null): void
    {
        Dissociate::dispatch($this, $product, $type);
    }

    /**
     * Return the customer groups relationship.
     *
     * @return BelongsToMany<CustomerGroup>
     */
    public function customerGroups(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                CustomerGroup::class,
                "{$prefix}customer_group_product"
            )
            ->withPivot(
                [
                    'purchasable',
                    'visible',
                    'enabled',
                    'starts_at',
                    'ends_at',
                ]
            )
            ->withTimestamps();
    }

    /**
     * Return the brand relationship.
     *
     * @return BelongsTo<Brand>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Apply the status scope.
     *
     * @param  Builder  $query
     * @param  string  $status
     * @return Builder
     */
    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->whereStatus($status);
    }

    /**
     * Return the prices relationship.
     *
     * @return HasManyThrough<Price>
     */
    public function prices(): HasManyThrough
    {
        return $this
            ->hasManyThrough(
                Price::class,
                ProductVariant::class,
                'product_id',
                'priceable_id'
            )
            ->wherePriceableType(ProductVariant::class);
    }
}
