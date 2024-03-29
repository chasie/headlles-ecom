<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasAttributes;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\ProductTypeFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class ProductType extends BaseModel
{
    use HasAttributes, HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ProductTypeFactory
    {
        return ProductTypeFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the mapped attributes relation.
     *
     * @return MorphToMany
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
     * Return the product attributes relationship.
     *
     * @return MorphToMany
     */
    public function productAttributes(): MorphToMany
    {
        return $this
            ->mappedAttributes()
            ->whereAttributeType(Product::class);
    }

    /**
     * Return the variant attributes relationship.
     *
     * @return MorphToMany
     */
    public function variantAttributes(): MorphToMany
    {
        return $this
            ->mappedAttributes()
            ->whereAttributeType(ProductVariant::class);
    }

    /**
     * Get the products relation.
     *
     * @return HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
