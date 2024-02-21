<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasDefaultRecord;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Database\Factories\CurrencyFactory;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property float $exchange_rate
 * @property int $decimal_places
 * @property bool $enabled
 * @property bool $default
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Currency extends BaseModel
{
    use HasDefaultRecord, HasFactory, HasMacros, LogsActivity;

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CurrencyFactory
    {
        return CurrencyFactory::new();
    }

    /**
     * Return the prices relationship
     *
     * @return HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(Price::class);
    }

    /**
     * Returns the amount we need to multiply or divide the price
     * for the cents/pence.
     *
     * @return string
     */
    public function getFactorAttribute(): string
    {
        /**
         * If we figure out how many decimal places we need, we can work
         * out what the initial divided value should be to get the cents.
         *
         * E.g. For two decimal places, we need to divide by 100.
         */
        if ($this->decimal_places < 1) {
            return 1;
        }

        return sprintf("1%0{$this->decimal_places}d", 0);
    }
}
