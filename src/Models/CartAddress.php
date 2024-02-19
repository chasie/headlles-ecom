<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\Addressable;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\CachesProperties;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\LogsActivity;
use Chasie\HeadlesEcom\Base\ValueObjects\Cart\TaxBreakdown;
use Chasie\HeadlesEcom\Database\Factories\CartAddressFactory;
use Chasie\HeadlesEcom\DataTypes\Price;
use Chasie\HeadlesEcom\DataTypes\ShippingOption;

/**
 * @property int $id
 * @property int $cart_id
 * @property ?int $country_id
 * @property ?string $title
 * @property ?string $first_name
 * @property ?string $last_name
 * @property ?string $company_name
 * @property ?string $line_one
 * @property ?string $line_two
 * @property ?string $line_three
 * @property ?string $city
 * @property ?string $state
 * @property ?string $postcode
 * @property ?string $delivery_instructions
 * @property ?string $contact_email
 * @property ?string $contact_phone
 * @property string $type
 * @property ?string $shipping_option
 * @property array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class CartAddress extends BaseModel implements Addressable
{
    use CachesProperties, HasFactory, HasMacros, LogsActivity;

    /**
     * Array of cachable class properties.
     *
     * @var array
     */
    public $cachableProperties = [
        'shippingOption',
        'shippingSubTotal',
        'shippingTaxTotal',
        'shippingTotal',
        'taxBreakdown',
    ];

    /**
     * The applied shipping option.
     */
    public ?ShippingOption $shippingOption = null;

    /**
     * The shipping sub total.
     */
    public ?Price $shippingSubTotal = null;

    /**
     * The shipping tax total.
     */
    public ?Price $shippingTaxTotal = null;

    /**
     * The shipping total.
     */
    public ?Price $shippingTotal = null;

    /**
     * The tax breakdown.
     *
     * @var TaxBreakdown
     */
    public ?TaxBreakdown $taxBreakdown = null;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CartAddressFactory
    {
        return CartAddressFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'country_id',
        'title',
        'first_name',
        'last_name',
        'company_name',
        'line_one',
        'line_two',
        'line_three',
        'city',
        'state',
        'postcode',
        'delivery_instructions',
        'contact_email',
        'contact_phone',
        'meta',
        'type',
        'shipping_option',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'meta' => AsArrayObject::class,
    ];

    /**
     * Return the cart relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Return the country relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
