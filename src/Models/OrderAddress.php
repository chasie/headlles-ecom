<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\Addressable;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasPersonalDetails;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Database\Factories\OrderAddressFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $order_id
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
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class OrderAddress extends BaseModel implements Addressable
{
    use HasFactory,
        HasMacros,
        HasPersonalDetails,
        LogsActivity;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): OrderAddressFactory
    {
        return OrderAddressFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
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
     * Return the order relationship.
     *
     * @return BelongsTo<Order>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Return the country relationship.
     *
     * @return BelongsTo<Country>
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}
