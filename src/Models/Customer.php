<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\AsAttributeData;
use HeadlessEcom\Base\Traits\HasAttributes;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasPersonalDetails;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Base\Traits\Searchable;
use HeadlessEcom\Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ?string $title
 * @property string $first_name
 * @property string $last_name
 * @property ?string $company_name
 * @property ?string $vat_no
 * @property ?string $account_ref
 * @property ?array $attribute_data
 * @property ?array $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Customer extends BaseModel
{
    use HasAttributes,
        HasFactory,
        HasMacros,
        HasPersonalDetails,
        HasTranslations,
        Searchable;

    /**
     * Define the guarded attributes.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'attribute_data' => AsAttributeData::class,
        'meta'           => AsArrayObject::class,
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CustomerFactory
    {
        return CustomerFactory::new();
    }

    /**
     * Return the customer group relationship.
     *
     * @return BelongsToMany
     */
    public function customerGroups(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                CustomerGroup::class,
                "{$prefix}customer_customer_group"
            )
            ->withTimestamps();
    }

    /**
     * Return the customer group relationship.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                config('auth.providers.users.model'),
                "{$prefix}customer_user"
            )
            ->withTimestamps();
    }

    /**
     * Return the addresses relationship.
     *
     * @return HasMany
     */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the mapped attributes relation.
     *
     * @return MorphToMany
     */
    public function mappedAttributes(): MorphToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->morphToMany(
                Attribute::class,
                'attributable',
                "{$prefix}attributables"
            )
            ->withTimestamps();
    }
}
