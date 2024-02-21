<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\CachesProperties;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Base\ValueObjects\Cart\TaxBreakdown;
use HeadlessEcom\Database\Factories\CartLineFactory;
use HeadlessEcom\DataTypes\Price;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $cart_id
 * @property string $purchasable_type
 * @property int $purchasable_id
 * @property int $quantity
 * @property ?array $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class CartLine extends BaseModel
{
    use CachesProperties, HasFactory, HasMacros, LogsActivity;

    /**
     * Array of cachable class properties.
     *
     * @var array
     */
    public $cachableProperties = [
        'unitPrice',
        'subTotal',
        'discountTotal',
        'taxAmount',
        'total',
        'promotionDescription',
        'taxBreakdown',
    ];

    /**
     * The cart line unit price.
     */
    public ?Price $unitPrice = null;

    /**
     * The cart line sub total.
     */
    public ?Price $subTotal = null;

    /**
     * The discounted sub total
     */
    public ?Price $subTotalDiscounted = null;

    /**
     * The discount total.
     */
    public ?Price $discountTotal = null;

    /**
     * The cart line tax amount.
     */
    public ?Price $taxAmount = null;

    /**
     * The cart line total.
     */
    public ?Price $total = null;

    /**
     * The promotion description.
     */
    public string $promotionDescription = '';

    /**
     * All the tax breakdowns for the cart line.
     */
    public TaxBreakdown $taxBreakdown;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CartLineFactory
    {
        return CartLineFactory::new();
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
        'quantity' => 'integer',
        'meta'     => AsArrayObject::class,
    ];

    /**
     * Return the cart relationship.
     *
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Return the tax class relationship.
     *
     * @return HasOneThrough
     */
    public function taxClass(): HasOneThrough
    {
        return $this
            ->hasOneThrough(
                related  : TaxClass::class,
                through  : $this->purchasable_type,
                firstKey : 'tax_class_id',
                secondKey: 'id'
            );
    }

    /**
     * @return BelongsToMany
     */
    public function discounts(): BelongsToMany
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this
            ->belongsToMany(
                Discount::class,
                "{$prefix}cart_line_discount"
            );
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
}
