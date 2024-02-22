<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasMedia;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Database\Factories\ProductOptionValueFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia as SpatieHasMedia;

/**
 * @property int $id
 * @property int $product_option_id
 * @property string $name
 * @property int $position
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class ProductOptionValue extends BaseModel implements SpatieHasMedia
{
    use HasFactory,
        HasMacros,
        HasMedia,
        HasTranslations;

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => AsCollection::class,
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ProductOptionValueFactory
    {
        return ProductOptionValueFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    protected function setNameAttribute($value): void
    {
        $this->attributes['name'] = json_encode($value);
    }

    public function getNameAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * @return BelongsTo
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    /**
     * @return BelongsToMany
     */
    public function variants(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                ProductVariant::class,
                "{$prefix}product_option_value_product_variant",
                'value_id',
                'variant_id',
        );
    }
}
