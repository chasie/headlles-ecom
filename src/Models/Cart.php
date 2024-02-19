<?php

namespace Chasie\HeadlesEcom\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Collection;
use Chasie\HeadlesEcom\Actions\Carts\AddAddress;
use Chasie\HeadlesEcom\Actions\Carts\AddOrUpdatePurchasable;
use Chasie\HeadlesEcom\Actions\Carts\AssociateUser;
use Chasie\HeadlesEcom\Actions\Carts\CreateOrder;
use Chasie\HeadlesEcom\Actions\Carts\GenerateFingerprint;
use Chasie\HeadlesEcom\Actions\Carts\RemovePurchasable;
use Chasie\HeadlesEcom\Actions\Carts\SetShippingOption;
use Chasie\HeadlesEcom\Actions\Carts\UpdateCartLine;
use Chasie\HeadlesEcom\Base\Addressable;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Purchasable;
use Chasie\HeadlesEcom\Base\Traits\CachesProperties;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\LogsActivity;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\DiscountBreakdown;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\FreeItem;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\Promotion;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\ShippingBreakdown;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\TaxBreakdown;
use Chasie\HeadlesEcom\Database\Factories\CartFactory;
use Chasie\HeadlesEcom\DataTypes\Price;
use Chasie\HeadlesEcom\DataTypes\ShippingOption;
use Chasie\HeadlesEcom\Exceptions\Carts\CartException;
use Chasie\HeadlesEcom\Exceptions\FingerprintMismatchException;
use Chasie\HeadlesEcom\Facades\DB;
use Chasie\HeadlesEcom\Facades\ShippingManifest;
use Chasie\HeadlesEcom\Pipelines\Cart\Calculate;
use Chasie\HeadlesEcom\Validation\Cart\ValidateCartForOrderCreation;

/**
 * @property int $id
 * @property ?int $user_id
 * @property ?int $customer_id
 * @property ?int $merged_id
 * @property int $currency_id
 * @property int $channel_id
 * @property ?int $order_id
 * @property ?string $coupon_code
 * @property ?\Illuminate\Support\Carbon $completed_at
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Cart extends BaseModel
{
    use CachesProperties;
    use HasFactory;
    use HasMacros;
    use LogsActivity;

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lines(): HasMany
    {
        return $this->hasMany(CartLine::class, 'cart_id', 'id');
    }

    /**
     * Return the currency relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    /**
     * Return the user relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'));
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

    public function scopeUnmerged($query)
    {
        return $query->whereNull('merged_id');
    }

    /**
     * Return the addresses relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(CartAddress::class, 'cart_id');
    }

    /**
     * Return the shipping address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function shippingAddress(): HasOne
    {
        return $this->hasOne(CartAddress::class, 'cart_id')->whereType('shipping');
    }

    /**
     * Return the billing address relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function billingAddress(): HasOne
    {
        return $this->hasOne(CartAddress::class, 'cart_id')->whereType('billing');
    }

    /**
     * Return the order relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Apply scope to get active cart.
     *
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
    public function hasCompletedOrders()
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
     * @return bool
     */
    public function addLines(iterable $lines)
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
        foreach (config('headless-ecom.cart.validators.remove_from_cart', []) as $action)
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
     * @param  array  $meta
     */
    public function updateLine(
        int  $cartLineId,
        int  $quantity,
             $meta = null,
        bool $refresh = true
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
     * @return \Chasie\HeadlesEcom\Models\Cart
     */
    public function updateLines(Collection $lines)
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
     */
    public function clear()
    {
        $this->lines()->delete();

        return $this->refresh()->calculate();
    }

    /**
     * Associate a user to the cart
     *
     * @param  string  $policy
     * @param  bool  $refresh
     * @return Cart
     */
    public function associate(User $user, $policy = 'merge', $refresh = true)
    {
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
                throw new Exception('Invalid user');
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
                throw new Exception('Invalid customer');
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
     * @return \Chasie\HeadlesEcom\Models\Cart
     */
    public function setShippingAddress(array|Addressable $address): Cart
    {
        return $this->addAddress($address, 'shipping');
    }

    /**
     * Set the billing address.
     *
     * @return \Chasie\HeadlesEcom\Models\Cart
     */
    public function setBillingAddress(array|Addressable $address): Cart
    {
        return $this->addAddress($address, 'billing');
    }

    /**
     * Set the shipping option to the shipping address.
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
    public function isShippable()
    {
        return (bool) $this
            ->lines
            ->filter(fn($line) => $line->purchasable->isShippable())
            ->count();
    }

    /**
     * Create an order from the Cart.
     *
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
    public function canCreateOrder()
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
    public function fingerprint()
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
     * @throws FingerprintMismatchException
     */
    public function checkFingerprint($fingerprint)
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
