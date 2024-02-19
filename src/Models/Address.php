<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\Addressable;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\AddressFactory;

/**
 * @property int $id
 * @property int $customer_id
 * @property ?string $title
 * @property string $first_name
 * @property string $last_name
 * @property ?string $company_name
 * @property string $line_one
 * @property ?string $line_two
 * @property ?string $line_three
 * @property string $city
 * @property ?string $state
 * @property ?string $postcode
 * @property int $country_id
 * @property ?string $delivery_instructions
 * @property ?string $contact_mail
 * @property ?string $contact_phone
 * @property ?\Illuminate\Support\Carbon $last_used_at
 * @property array $meta
 * @property bool $shipping_default
 * @property bool $billing_default
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Address extends BaseModel implements Addressable
{
    use HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): AddressFactory
    {
        return AddressFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Define attribute casting.
     *
     * @var array
     */
    protected $casts = [
        'billing_default' => 'boolean',
        'meta' => AsArrayObject::class,
        'shipping_default' => 'boolean',
    ];

    /**
     * Return the country relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
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
}
