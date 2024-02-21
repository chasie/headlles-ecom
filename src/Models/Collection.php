<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Kalnoy\Nestedset\NodeTrait;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\AsAttributeData;
use HeadlessEcom\Base\Traits\HasChannels;
use HeadlessEcom\Base\Traits\HasCustomerGroups;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasMedia;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Base\Traits\HasUrls;
use HeadlessEcom\Base\Traits\Searchable;
use HeadlessEcom\Database\Factories\CollectionFactory;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property int $collection_group_id
 * @property-read  int $_lft
 * @property-read  int $_rgt
 * @property ?int $parent_id
 * @property string $type
 * @property ?array $attribute_data
 * @property string $sort
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 */
class Collection extends BaseModel implements SpatieHasMedia
{
    use HasChannels,
        HasCustomerGroups,
        HasFactory,
        HasMacros,
        HasMedia,
        HasTranslations,
        HasUrls,
        NodeTrait,
        Searchable
    {
        NodeTrait::usesSoftDelete insteadof Searchable;
    }

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
    ];

    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CollectionFactory
    {
        return CollectionFactory::new();
    }

    /**
     * Return the group relationship.
     *
     * @return BelongsTo
     */
    public function group()
    {
        return $this
            ->belongsTo(
                CollectionGroup::class,
                'collection_group_id'
            );
    }

    /**
     * @param  Builder  $builder
     * @param $id
     * @return Builder
     */
    public function scopeInGroup(Builder $builder, $id): Builder
    {
        return $builder->where('collection_group_id', $id);
    }

    /**
     * Return the products relationship.
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Product::class,
                "{$prefix}collection_product"
            )
            ->withPivot(
                [
                    'position',
                ]
            )
            ->withTimestamps()->orderByPivot('position');
    }

    /**
     * Get the translated name of ancestor collections.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getBreadcrumbAttribute(): \Illuminate\Support\Collection
    {
        return $this
            ->ancestors
            ->map(fn($ancestor) => $ancestor->translateAttribute('name'));
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
                "{$prefix}collection_customer_group"
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
}
