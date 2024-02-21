<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\Price;
use HeadlessEcom\Base\Casts\TaxBreakdown;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Database\Factories\OrderLineFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $order_id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property string $type
 * @property string $description
 * @property ?string $option
 * @property string $identifier
 * @property int $unit_price
 * @property int $unit_quantity
 * @property int $quantity
 * @property int $sub_total
 * @property int $discount_total
 * @property array $tax_breakdown
 * @property int $tax_total
 * @property int $total
 * @property ?string $notes
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class OrderLine extends BaseModel
{
    use HasFactory,
        HasMacros,
        LogsActivity;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): OrderLineFactory
    {
        return OrderLineFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_quantity'  => 'integer',
        'quantity'       => 'integer',
        'meta'           => AsArrayObject::class,
        'tax_breakdown'  => TaxBreakdown::class,
        'unit_price'     => Price::class,
        'sub_total'      => Price::class,
        'tax_total'      => Price::class,
        'discount_total' => Price::class,
        'total'          => Price::class,
    ];

    /**
     * Return the order relationship.
     *
     * @return BelongsTo<Order>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Return the polymorphic relation.
     *
     * @return MorphTo
     */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the currency relationship.
     *
     * @return HasOneThrough<Currency>
     */
    public function currency(): HasOneThrough
    {
        return $this
            ->hasOneThrough(
                Currency::class,
                Order::class,
                'id',
                'code',
                'order_id',
                'currency_code'
            );
    }
}
