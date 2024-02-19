<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Casts\AsAttributeData;
use Chasie\HeadlesEcom\Base\Traits\HasAttributes;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasMedia;
use Chasie\HeadlesEcom\Base\Traits\HasTranslations;
use Chasie\HeadlesEcom\Base\Traits\HasUrls;
use Chasie\HeadlesEcom\Base\Traits\LogsActivity;
use Chasie\HeadlesEcom\Base\Traits\Searchable;
use Chasie\HeadlesEcom\Database\Factories\BrandFactory;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property string $name
 * @property ?array $attribute_data
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Brand extends BaseModel implements SpatieHasMedia
{
    use HasAttributes,
        HasFactory,
        HasMacros,
        HasMedia,
        HasTranslations,
        HasUrls,
        LogsActivity,
        Searchable;

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): BrandFactory
    {
        return BrandFactory::new();
    }

    /**
     * Get the mapped attributes relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function mappedAttributes()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->morphToMany(
                Attribute::class,
                'attributable',
                "{$prefix}attributables"
            )
            ->withTimestamps();
    }

    /**
     * Return the product relationship.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
