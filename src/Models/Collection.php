<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Casts\AsAttributeData;
use Chasie\HeadlesEcom\Base\Traits\HasChannels;
use Chasie\HeadlesEcom\Base\Traits\HasCustomerGroups;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasMedia;
use Chasie\HeadlesEcom\Base\Traits\HasTranslations;
use Chasie\HeadlesEcom\Base\Traits\HasUrls;
use Chasie\HeadlesEcom\Base\Traits\Searchable;
use Chasie\HeadlesEcom\Database\Factories\CollectionFactory;
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
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this
            ->belongsTo(
                CollectionGroup::class,
                'collection_group_id'
            );
    }

    public function scopeInGroup(Builder $builder, $id)
    {
        return $builder->where('collection_group_id', $id);
    }

    /**
     * Return the products relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
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
     * @return Illuminate\Support\Collection
     */
    public function getBreadcrumbAttribute()
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
