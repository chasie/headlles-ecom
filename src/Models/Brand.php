<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\AsAttributeData;
use HeadlessEcom\Base\Traits\HasAttributes;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasMedia;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Base\Traits\HasUrls;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Base\Traits\Searchable;
use HeadlessEcom\Database\Factories\BrandFactory;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property string $name
 * @property ?array $attribute_data
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
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
     * @return MorphToMany<Attribute>
     */
    public function mappedAttributes(): MorphToMany
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
