<?php

namespace HeadlessEcom\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use HeadlessEcom\Actions\Carts\AddAddress;
use HeadlessEcom\Actions\Carts\AddOrUpdatePurchasable;
use HeadlessEcom\Actions\Carts\AssociateUser;
use HeadlessEcom\Actions\Carts\CreateOrder;
use HeadlessEcom\Actions\Carts\GenerateFingerprint;
use HeadlessEcom\Actions\Carts\RemovePurchasable;
use HeadlessEcom\Actions\Carts\SetShippingOption;
use HeadlessEcom\Actions\Carts\UpdateCartLine;
use HeadlessEcom\Base\Addressable;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Purchasable;
use HeadlessEcom\Base\Traits\CachesProperties;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Base\ValueObjects\Cart\DiscountBreakdown;
use HeadlessEcom\Base\ValueObjects\Cart\FreeItem;
use HeadlessEcom\Base\ValueObjects\Cart\Promotion;
use HeadlessEcom\Base\ValueObjects\Cart\ShippingBreakdown;
use HeadlessEcom\Base\ValueObjects\Cart\TaxBreakdown;
use HeadlessEcom\Database\Factories\CartFactory;
use HeadlessEcom\DataTypes\Price;
use HeadlessEcom\DataTypes\ShippingOption;
use HeadlessEcom\Exceptions\Carts\CartException;
use HeadlessEcom\Exceptions\FingerprintMismatchException;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Facades\ShippingManifest;
use HeadlessEcom\Pipelines\Cart\Calculate;
use HeadlessEcom\Validation\Cart\ValidateCartForOrderCreation;
use Throwable;

/**
 * @property int $id
 * @property ?int $user_id
 * @property ?int $customer_id
 * @property ?int $merged_id
 * @property int $currency_id
 * @property int $channel_id
 * @property ?int $order_id
 * @property ?string $coupon_code
 * @property ?Carbon $completed_at
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Cart extends BaseModel
{
    use CachesProperties, HasFactory, HasMacros, LogsActivity;

    /**
     * Array of cachable class properties.
     *
     * @var array
     */
    public $cachableProperties = [
        'subTotal',
        'shippingTotal',
        'taxTotal',
        'discounts',
        'discountTotal',
        'discountBreakdown',
        'total',
        'taxBreakdown',
        'promotions',
        'freeItems',
    ];

    /**
     * The cart sub total.
     * Sum of cart line amounts, before tax, shipping and cart-level discounts.
     */
    public ?Price $subTotal = null;

    /**
     * The cart sub total.
     * Sum of cart line amounts, before tax, shipping minus discount totals.
     */
    public ?Price $subTotalDiscounted = null;

    /**
     * The shipping sub total for the cart.
     */
    public ?Price $shippingSubTotal = null;

    /**
     * The shipping total for the cart.
     */
    public ?Price $shippingTotal = null;

    /**
     * The cart tax total.
     * Sum of all tax to pay across cart lines and shipping.
     */
    public ?Price $taxTotal = null;

    /**
     * The discount total.
     * Sum of all cart line discounts and cart-level discounts.
     */
    public ?Price $discountTotal = null;

    /**
     * All the discount breakdowns for the cart.
     *
     * @var null|Collection<DiscountBreakdown>
     */
    public ?Collection $discountBreakdown = null;

    /**
     * The shipping override to use for the cart.
     */
    public ?ShippingOption $shippingOptionOverride = null;

    /**
     * Additional shipping estimate meta data.
     */
    public array $shippingEstimateMeta = [];

    /**
     * All the shipping breakdowns for the cart.
     */
    public ?ShippingBreakdown $shippingBreakdown = null;

    /**
     * The cart total.
     * Sum of the cart-line amounts, shipping and tax, minus cart-level discount amount.
     */
    public ?Price $total = null;

    /**
     * All the tax breakdowns for the cart.
     *
     * @var null|Collection<TaxBreakdown>
     */
    public ?TaxBreakdown $taxBreakdown = null;

    /**
     * The cart-level promotions.
     *
     * @var null|Collection<Promotion>
     */
    public ?Collection $promotions = null;

    /**
     * The cart-level discounts.
     *
     * @var null|Collection<Discount>
     */
    public ?Collection $discounts = null;

    /**
     * Qualifying promotional free items.
     *
     * @var null|Collection<FreeItem>
     */
    public ?Collection $freeItems = null;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CartFactory
    {
        return CartFactory::new();
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
        'completed_at' => 'datetime',
        'meta'         => AsArrayObject::class,
    ];

    /**
     * Return the cart lines relationship.
     *
     * @return HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(CartLine::class, 'cart_id', 'id');
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
     * Return the user relationship.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
    }

    /**
     * Return the customer relationship.
     *
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeUnmerged($query)
    {
        return $query->whereNull('merged_id');
    }

    /**
     * Return the addresses relationship.
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CartAddress::class, 'cart_id');
    }

    /**
     * Return the shipping address relationship.
     *
     * @return HasOne
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(CartAddress::class, 'cart_id')->whereType('shipping');
    }

    /**
     * Return the billing address relationship.
     *
     * @return HasOne
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(CartAddress::class, 'cart_id')->whereType('billing');
    }

    /**
     * Return the order relationship.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Apply scope to get active cart.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereDoesntHave('orders')
            ->orWhereHas(
                'orders',
                function ($query)
                {
                    return $query->whereNull('placed_at');
                }
            );
    }

    /**
     * Return the draft order relationship.
     *
     * @param  int|null  $draftOrderId
     * @return HasOne
     */
    public function draftOrder(int $draftOrderId = null): HasOne
    {
        return $this
            ->hasOne(Order::class)
            ->when(
                $draftOrderId,
                function (Builder $query, int $draftOrderId)
                {
                    $query->where('id', $draftOrderId);
                }
            )
            ->whereNull('placed_at');
    }

    /**
     * Return the completed order relationship.
     *
     * @param  int|null  $completedOrderId
     * @return HasOne
     */
    public function completedOrder(int $completedOrderId = null): HasOne
    {
        return $this
            ->hasOne(Order::class)
            ->when(
                $completedOrderId,
                function (Builder $query, int $completedOrderId)
                {
                    $query->where('id', $completedOrderId);
                }
            )
            ->whereNotNull('placed_at');
    }

    /**
     * Return the carts completed order.
     *
     * @return HasMany
     */
    public function completedOrders(): HasMany
    {
        return $this
            ->hasMany(Order::class)
            ->whereNotNull('placed_at');
    }

    /**
     * Return whether the cart has any completed order.
     *
     * @return bool
     */
    public function hasCompletedOrders(): bool
    {
        return (bool) $this->completedOrders()->count();
    }

    /**
     * Calculate the cart totals and cache the result.
     */
    public function calculate(): Cart
    {
        $cart = app(Pipeline::class)
            ->send($this)
            ->through(
                config(
                    'headless-ecom.cart.pipelines.cart',
                    [
                        Calculate::class,
                    ]
                )
            )
            ->thenReturn();

        return $cart->cacheProperties();
    }

    /**
     * Add or update a purchasable item to the cart
     */
    public function add(
        Purchasable $purchasable,
        int         $quantity = 1,
        array       $meta = [],
        bool        $refresh = true
    ): Cart {
        foreach (config('headless-ecom.cart.validators.add_to_cart', []) as $action)
        {
            // Throws a validation exception?
            app($action)
                ->using(
                    cart       : $this,
                    purchasable: $purchasable,
                    quantity   : $quantity,
                    meta       : $meta
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.add_to_cart',
                AddOrUpdatePurchasable::class
            )
        )
            ->execute($this, $purchasable, $quantity, $meta)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Add cart lines.
     *
     * @param  iterable  $lines
     * @return Cart
     */
    public function addLines(iterable $lines): Cart
    {
        DB::transaction(
            function () use ($lines)
            {
                collect($lines)->each(
                    function ($line)
                    {
                        $this->add(
                            purchasable: $line['purchasable'],
                            quantity   : $line['quantity'],
                            meta       : (array) ($line['meta'] ?? null),
                            refresh    : false
                        );
                    }
                );
            }
        );

        return $this->refresh()->calculate();
    }

    /**
     * Remove a cart line
     */
    public function remove(int $cartLineId, bool $refresh = true): Cart
    {
        foreach (
            config('headless-ecom.cart.validators.remove_from_cart', []) as $action
        )
        {
            app($action)
                ->using(
                    cart      : $this,
                    cartLineId: $cartLineId,
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.remove_from_cart',
                RemovePurchasable::class
            )
        )
            ->execute($this, $cartLineId)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Update cart line
     *
     * @param  int  $cartLineId
     * @param  int  $quantity
     * @param  array|null  $meta
     * @param  bool  $refresh
     * @return Cart
     */
    public function updateLine(
        int   $cartLineId,
        int   $quantity,
        array $meta = null,
        bool  $refresh = true
    ): Cart {
        foreach (config('headless-ecom.cart.validators.update_cart_line', []) as $action)
        {
            app($action)
                ->using(
                    cart      : $this,
                    cartLineId: $cartLineId,
                    quantity  : $quantity,
                    meta      : $meta
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.update_cart_line',
                UpdateCartLine::class
            )
        )
            ->execute($cartLineId, $quantity, $meta)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Update cart lines.
     *
     * @param  Collection  $lines
     * @return Cart
     */
    public function updateLines(Collection $lines): Cart
    {
        DB::transaction(
            function () use ($lines)
            {
                $lines->each(
                    function ($line)
                    {
                        $this->updateLine(
                            cartLineId: $line['id'],
                            quantity  : $line['quantity'],
                            meta      : $line['meta'] ?? null,
                            refresh   : false
                        );
                    }
                );
            }
        );

        return $this->refresh()->calculate();
    }

    /**
     * Deletes all cart lines.
     *
     * @return Cart
     */
    public function clear(): Cart
    {
        $this->lines()->delete();

        return $this->refresh()->calculate();
    }

    /**
     * Associate a user to the cart
     *
     * @param  User  $user
     * @param  string  $policy
     * @param  bool  $refresh
     * @return Cart
     * @throws Exception
     */
    public function associate(
        User   $user,
        string $policy = 'merge',
        bool   $refresh = true
    ): Cart {
        if ($this->customer()->exists())
        {
            if (
                !$user
                    ->query()
                    ->whereHas(
                        'customers',
                        fn($query) => $query->where('customer_id', $this->customer->id)
                    )
                    ->exists()
            )
            {
                throw new Exception(__('headless-ecom::exceptions/base.user-invalid'));
            }
        }

        return app(
            config(
                'headless-ecom.cart.actions.associate_user',
                AssociateUser::class
            )
        )
            ->execute($this, $user, $policy)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Associate a customer to the cart
     *
     * @param  Customer  $customer
     * @return Cart
     * @throws Exception
     */
    public function setCustomer(Customer $customer): Cart
    {
        if ($this->user()->exists())
        {
            if (
                !$customer
                    ->query()
                    ->whereHas(
                        'users',
                        fn($query) => $query->where('user_id', $this->user->id)
                    )
                    ->exists()
            )
            {
                throw new Exception(__('headless-ecom::exceptions/base.customer-invalid'));
            }
        }

        $this->customer()->associate($customer)->save();

        return $this->refresh()->calculate();
    }

    /**
     * Add an address to the Cart.
     */
    public function addAddress(
        array|Addressable $address,
        string            $type,
        bool              $refresh = true
    ): Cart {
        foreach (config('headless-ecom.cart.validators.add_address', []) as $action)
        {
            app($action)
                ->using(
                    cart   : $this,
                    address: $address,
                    type   : $type,
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.add_address',
                AddAddress::class
            )
        )
            ->execute($this, $address, $type)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Set the shipping address.
     *
     * @param  array|Addressable  $address
     * @return Cart
     */
    public function setShippingAddress(array|Addressable $address): Cart
    {
        return $this->addAddress($address, 'shipping');
    }

    /**
     * Set the billing address.
     *
     * @param  array|Addressable  $address
     * @return Cart
     */
    public function setBillingAddress(array|Addressable $address): Cart
    {
        return $this->addAddress($address, 'billing');
    }

    /**
     * Set the shipping option to the shipping address.
     *
     * @param  ShippingOption  $option
     * @param  bool  $refresh
     * @return Cart
     */
    public function setShippingOption(ShippingOption $option, $refresh = true): Cart
    {
        foreach (config('headless-ecom.cart.validators.set_shipping_option', []) as $action)
        {
            app($action)
                ->using(
                    cart          : $this,
                    shippingOption: $option,
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.set_shipping_option',
                SetShippingOption::class
            )
        )
            ->execute($this, $option)
            ->then(fn() => $refresh ? $this->refresh()->calculate() : $this);
    }

    /**
     * Get the shipping option for the cart
     *
     * @return ShippingOption|null
     */
    public function getShippingOption(): ?ShippingOption
    {
        return ShippingManifest::getShippingOption($this);
    }

    /**
     * Returns whether the cart has shippable items.
     *
     * @return bool
     */
    public function isShippable(): bool
    {
        return (bool) $this
            ->lines
            ->filter(fn($line) => $line->purchasable->isShippable())
            ->count();
    }

    /**
     * Create an order from the Cart.
     *
     * @param  bool  $allowMultipleOrders
     * @param  int|null  $orderIdToUpdate
     * @return Order
     */
    public function createOrder(
        bool $allowMultipleOrders = false,
        int  $orderIdToUpdate = null
    ): Order {
        foreach (
            config(
                'headless-ecom.cart.validators.order_create',
                [
                    ValidateCartForOrderCreation::class,
                ]
            ) as $action
        )
        {
            app($action)
                ->using(
                    cart: $this,
                )
                ->validate();
        }

        return app(
            config(
                'headless-ecom.cart.actions.order_create',
                CreateOrder::class
            )
        )
            ->execute(
                $this->refresh()->calculate(),
                $allowMultipleOrders,
                $orderIdToUpdate
            )
            ->then(fn($order) => $order->refresh());
    }

    /**
     * Returns whether a cart has enough info to create an order.
     *
     * @return bool
     */
    public function canCreateOrder(): bool
    {
        $passes = true;

        foreach (
            config(
                'headless-ecom.cart.validators.order_create',
                [
                    ValidateCartForOrderCreation::class,
                ]
            ) as $action
        )
        {
            try
            {
                app($action)
                    ->using(
                        cart: $this,
                    )
                    ->validate();
            } catch (CartException $e)
            {
                $passes = false;
            }
        }

        return $passes;
    }

    /**
     * Get a unique fingerprint for the cart to identify if the contents have changed.
     *
     * @return string
     */
    public function fingerprint(): string
    {
        $generator = config(
            'headless-ecom.cart.fingerprint_generator',
            GenerateFingerprint::class
        );

        return (new $generator())->execute($this);
    }

    /**
     * Check whether a given fingerprint matches the one being generated for the cart.
     *
     * @param  string  $fingerprint
     * @return bool
     *
     * @throws FingerprintMismatchException|Throwable
     */
    public function checkFingerprint($fingerprint): bool
    {
        return tap(
            $fingerprint == $this->fingerprint(), function ($result)
        {
            throw_unless(
                $result,
                FingerprintMismatchException::class
            );
        }
        );
    }

    /**
     * Return the estimated shipping cost for a cart.
     *
     * @param  array  $params
     * @param  bool  $setOverride
     * @return ShippingOption|null
     */
    public function getEstimatedShipping(array $params, bool $setOverride = false): ?ShippingOption
    {
        $this->shippingEstimateMeta = $params;
        $option = ShippingManifest::getOptions($this)
            ->filter(fn($option) => !$option->collect)
            ->sortBy('price.value')->first();

        if ($setOverride && $option)
        {
            $this->shippingOptionOverride = $option;
        }

        return $option;
    }
}
