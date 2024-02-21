<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\DiscountBreakdown;
use HeadlessEcom\Base\Casts\Price;
use HeadlessEcom\Base\Casts\ShippingBreakdown;
use HeadlessEcom\Base\Casts\TaxBreakdown;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasTags;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Base\Traits\Searchable;
use HeadlessEcom\Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

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
 * @property ?Carbon $placed_at
 * @property ?array $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
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
    public function getStatusLabelAttribute(): string
    {
        $statuses = config('headless-ecom.orders.statuses');

        return $statuses[$this->status]['label'] ?? $this->status;
    }

    /**
     * Return the channel relationship.
     *
     * @return BelongsTo<Channel>
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Return the cart relationship.
     *
     * @return BelongsTo<Cart>
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Return the lines relationship.
     *
     * @return HasMany<OrderLine>
     */
    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }

    /**
     * Return physical product lines relationship.
     *
     * @return HasMany<OrderLine>
     */
    public function physicalLines(): HasMany
    {
        return $this->lines()->whereType('physical');
    }

    /**
     * Return digital product lines relationship.
     *
     * @return HasMany<OrderLine>
     */
    public function digitalLines(): HasMany
    {
        return $this->lines()->whereType('digital');
    }

    /**
     * Return shipping lines relationship.
     *
     * @return HasMany<OrderLine>
     */
    public function shippingLines(): HasMany
    {
        return $this->lines()->whereType('shipping');
    }

    /**
     * Return product lines relationship.
     *
     * @return HasMany<OrderLine>
     */
    public function productLines(): HasMany
    {
        return $this->lines()->where('type', '!=', 'shipping');
    }

    /**
     * Return the currency relationship.
     *
     * @return BelongsTo<Currency>
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_code', 'code');
    }

    /**
     * Return the addresses relationship.
     *
     * @return HasMany<OrderAddress>
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(OrderAddress::class, 'order_id');
    }

    /**
     * Return the shipping address relationship.
     *
     * @return HasOne<OrderAddress>
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
     * @return HasOne<OrderAddress>
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
     * @return HasMany<Transaction>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Return the charges relationship.
     *
     * @return HasMany<Transaction>
     */
    public function captures(): HasMany
    {
        return $this->transactions()->whereType('capture');
    }

    /**
     * Return the charges relationship.
     *
     * @return HasMany<Transaction>
     */
    public function intents(): HasMany
    {
        return $this->transactions()->whereType('intent');
    }

    /**
     * Return the refunds relationship.
     *
     * @return HasMany<Transaction>
     */
    public function refunds(): HasMany
    {
        return $this->transactions()->whereType('refund');
    }

    /**
     * Return the customer relationship.
     *
     * @return BelongsTo<Customer>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Return the user relationship.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this
            ->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Determines if this is a draft order.
     *
     * @return bool
     */
    public function isDraft(): bool
    {
        return !$this->isPlaced();
    }

    /**
     * Determines if this is a placed order.
     *
     * @return bool
     */
    public function isPlaced(): bool
    {
        return !blank($this->placed_at);
    }
}
