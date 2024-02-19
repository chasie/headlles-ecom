<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Casts\DiscountBreakdown;
use Chasie\HeadlesEcom\Base\Casts\Price;
use Chasie\HeadlesEcom\Base\Casts\ShippingBreakdown;
use Chasie\HeadlesEcom\Base\Casts\TaxBreakdown;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasTags;
use Chasie\HeadlesEcom\Base\Traits\LogsActivity;
use Chasie\HeadlesEcom\Base\Traits\Searchable;
use Chasie\HeadlesEcom\Database\Factories\OrderFactory;

/**
 * @property int $id
 * @property ?int $customer_id
 * @property ?int $user_id
 * @property int $channel_id
 * @property bool $new_customer
 * @property string $status
 * @property ?string $reference
 * @property ?string $customer_reference
 * @property int $sub_total
 * @property int $discount_total
 * @property array $discount_breakdown
 * @property array $shipping_breakdown
 * @property array $tax_breakdown
 * @property int $tax_total
 * @property int $total
 * @property ?string $notes
 * @property string $currency
 * @property ?string $compare_currency_code
 * @property float $exchange_rate
 * @property ?\Illuminate\Support\Carbon $placed_at
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Order extends BaseModel
{
    use HasFactory,
        HasMacros,
        HasTags,
        LogsActivity,
        Searchable;

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'tax_breakdown'      => TaxBreakdown::class,
        'meta'               => AsArrayObject::class,
        'placed_at'          => 'datetime',
        'sub_total'          => Price::class,
        'discount_total'     => Price::class,
        'discount_breakdown' => DiscountBreakdown::class,
        'shipping_breakdown' => ShippingBreakdown::class,
        'tax_total'          => Price::class,
        'total'              => Price::class,
        'shipping_total'     => Price::class,
        'new_customer'       => 'boolean',
    ];

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): OrderFactory
    {
        return OrderFactory::new();
    }

    /**
     * Getter for status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statuses = config('headless-ecom.orders.statuses');

        return $statuses[$this->status]['label'] ?? $this->status;
    }

    /**
     * Return the channel relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Return the cart relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Return the lines relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lines(): BelongsTo
    {
        return $this->hasMany(OrderLine::class);
    }

    /**
     * Return physical product lines relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physicalLines(): HasMany
    {
        return $this->lines()->whereType('physical');
    }

    /**
     * Return digital product lines relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function digitalLines(): HasMany
    {
        return $this->lines()->whereType('digital');
    }

    /**
     * Return shipping lines relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingLines(): HasMany
    {
        return $this->lines()->whereType('shipping');
    }

    /**
     * Return product lines relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productLines(): HasMany
    {
        return $this->lines()->where('type', '!=', 'shipping');
    }

    /**
     * Return the currency relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Return the addresses relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class, 'order_id');
    }

    /**
     * Return the shipping address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress(): HasOne
    {
        return $this
            ->hasOne(OrderAddress::class, 'order_id')
            ->whereType('shipping');
    }

    /**
     * Return the billing address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingAddress(): HasOne
    {
        return $this
            ->hasOne(OrderAddress::class, 'order_id')
            ->whereType('billing');
    }

    /**
     * Return the transactions relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Return the charges relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function captures(): HasMany
    {
        return $this->transactions()->whereType('capture');
    }

    /**
     * Return the charges relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function intents(): HasMany
    {
        return $this->transactions()->whereType('intent');
    }

    /**
     * Return the refunds relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function refunds(): HasMany
    {
        return $this->transactions()->whereType('refund');
    }

    /**
     * Return the customer relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Return the user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this
            ->belongsTo(
                config('auth.providers.users.model')
            );
    }

    /**
     * Determines if this is a draft order.
     */
    public function isDraft(): bool
    {
        return !$this->isPlaced();
    }

    /**
     * Determines if this is a placed order.
     */
    public function isPlaced(): bool
    {
        return !blank($this->placed_at);
    }
}
