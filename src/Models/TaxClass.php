<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasDefaultRecord;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\TaxClassFactory;

/**
 * @property int $id
 * @property string $name
 * @property bool $default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class TaxClass extends BaseModel
{
    use HasDefaultRecord,
        HasFactory,
        HasMacros;

    public static function booted()
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
