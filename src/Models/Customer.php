<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Casts\AsAttributeData;
use Chasie\HeadlesEcom\Base\Traits\HasAttributes;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasPersonalDetails;
use Chasie\HeadlesEcom\Base\Traits\HasTranslations;
use Chasie\HeadlesEcom\Base\Traits\Searchable;
use Chasie\HeadlesEcom\Database\Factories\CustomerFactory;

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
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customerGroups()
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the mapped attributes relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function mappedAttributes()
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
