<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasDefaultRecord;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\TaxZoneFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property string $name
 * @property string $zone_type
 * @property string $price_display
 * @property bool $active
 * @property bool $default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class TaxZone extends BaseModel
{
    use HasDefaultRecord,
        HasFactory,
        HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TaxZoneFactory
    {
        return TaxZoneFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Define the attribute casting.
     *
     * @var array
     */
    protected $casts = [
        'active' => 'boolean',
        'default' => 'boolean',
    ];

    /**
     * Return the countries relationship.
     *
     * @return HasMany
     */
    public function countries(): HasMany
    {
        return $this->hasMany(TaxZoneCountry::class);
    }

    /**
     * Return the states relationship.
     *
     * @return HasMany
     */
    public function states(): HasMany
    {
        return $this->hasMany(TaxZoneState::class);
    }

    /**
     * Return the postcodes relationship.
     *
     * @return HasMany
     */
    public function postcodes(): HasMany
    {
        return $this->hasMany(TaxZonePostcode::class);
    }

    /**
     * Return the customer groups relationship.
     *
     * @return HasMany
     */
    public function customerGroups(): HasMany
    {
        return $this->hasMany(TaxZoneCustomerGroup::class);
    }

    /**
     * Return the tax rates relationship.
     *
     * @return HasMany
     */
    public function taxRates(): HasMany
    {
        return $this->hasMany(TaxRate::class);
    }

    /**
     * Return the tax amounts relationship.
     *
     * @return HasManyThrough
     */
    public function taxAmounts(): HasManyThrough
    {
        return $this->hasManyThrough(
            TaxRateAmount::class,
            TaxRate::class
        );
    }
}
