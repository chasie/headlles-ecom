<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\Price as CastsPrice;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\PriceFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use Spatie\LaravelBlink\BlinkFacade as Blink;

/**
 * @property int $id
 * @property ?int $customer_group_id
 * @property ?int $currency_id
 * @property string $priceable_type
 * @property int $priceable_id
 * @property \HeadlessEcom\DataTypes\Price $price
 * @property ?int $compare_price
 * @property int $tier
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Price extends BaseModel
{
    use HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): PriceFactory
    {
        return PriceFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'price'         => CastsPrice::class,
        'compare_price' => CastsPrice::class,
    ];

    /**
     * Return the priceable relationship.
     *
     * @return MorphTo
     */
    public function priceable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the currency relationship.
     *
     * @return BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Return the customer group relationship.
     *
     * @return BelongsTo
     */
    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }

    /**
     * Return the price exclusive of tax.
     *
     * @return \HeadlessEcom\DataTypes\Price
     */
    public function priceExTax(): \HeadlessEcom\DataTypes\Price
    {
        if (!prices_inc_tax())
        {
            return $this->price;
        }

        $priceExTax = clone $this->price;

        $priceExTax->value = (int) round($priceExTax->value / (1 + $this->getPriceableTaxRate()));

        return $priceExTax;
    }

    /**
     * Return the price inclusive of tax.
     *
     * @return \HeadlessEcom\DataTypes\Price
     */
    public function priceIncTax(): \HeadlessEcom\DataTypes\Price
    {
        if (prices_inc_tax())
        {
            return $this->price;
        }

        $priceIncTax = clone $this->price;
        $priceIncTax->value = (int) round($priceIncTax->value * (1 + $this->getPriceableTaxRate()));

        return $priceIncTax;
    }

    /**
     * Return the total tax rate amount within the predefined tax zone for the related priceable
     *
     * @return int|float
     */
    protected function getPriceableTaxRate(): int|float
    {
        return Blink::once(
            'price_tax_rate_'.$this->priceable->getTaxClass()->id,
            function ()
            {
                $taxZone = TaxZone::where('default', '=', 1)->first();

                if (
                    $taxZone &&
                    !is_null($taxClass = $this->priceable->getTaxClass())
                )
                {
                    return $taxClass->taxRateAmounts
                            ->whereIn('tax_rate_id', $taxZone->taxRates->pluck('id'))
                            ->sum('percentage') / 100;
                }

                return 0;
            }
        );
    }
}
