<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\TaxRateAmountFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ?int $tax_class_id
 * @property ?int $tax_rate_id
 * @property float $percentage
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class TaxRateAmount extends BaseModel
{
    use HasFactory, HasMacros;

    /**
     * The tax rate amount.
     *
     * @var Price|null
     */
    public $total;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TaxRateAmountFactory
    {
        return TaxRateAmountFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Return the tax rate relation.
     *
     * @return BelongsTo
     */
    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    /**
     * Return the tax class relation.
     *
     * @return BelongsTo
     */
    public function taxClass(): BelongsTo
    {
        return $this->belongsTo(TaxClass::class);
    }
}
