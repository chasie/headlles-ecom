<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\TaxRateFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ?int $tax_zone_id
 * @property bool $priority
 * @property string $name
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class TaxRate extends BaseModel
{
    use HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TaxRateFactory
    {
        return TaxRateFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return the tax zone relation.
     *
     * @return BelongsTo
     */
    public function taxZone(): BelongsTo
    {
        return $this->belongsTo(TaxZone::class);
    }

    /**
     * Return the tax rate amounts relation.
     *
     * @return HasMany
     */
    public function taxRateAmounts(): HasMany
    {
        return $this->hasMany(TaxRateAmount::class);
    }
}
