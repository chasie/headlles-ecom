<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasDefaultRecord;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\TaxClassFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property bool $default
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class TaxClass extends BaseModel
{
    use HasDefaultRecord,
        HasFactory,
        HasMacros;

    /**
     * @return void
     */
    public static function booted(): void
    {
        static::updated(
            function ($taxClass)
            {
                if ($taxClass->default)
                {
                    TaxClass::whereDefault(true)
                        ->where('id', '!=', $taxClass->id)
                        ->update(
                            [
                                'default' => false,
                            ]
                        );
                }
            }
        );

        static::created(
            function ($taxClass)
            {
                if ($taxClass->default)
                {
                    TaxClass::whereDefault(true)
                        ->where('id', '!=', $taxClass->id)
                        ->update(
                            [
                                'default' => false,
                            ]
                        );
                }
            }
        );
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TaxClassFactory
    {
        return TaxClassFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return the tax rate amounts relationship.
     *
     * @return HasMany
     */
    public function taxRateAmounts(): HasMany
    {
        return $this->hasMany(TaxRateAmount::class);
    }

    /**
     * Return the ProductVariants relationship.
     *
     * @return HasMany
     */
    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }
}
