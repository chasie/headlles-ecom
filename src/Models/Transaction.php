<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Casts\Price;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Database\Factories\TransactionFactory;
use HeadlessEcom\Facades\Payments;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property ?int $parent_transaction_id
 * @property int $order_id
 * @property bool $success
 * @property string $type
 * @property string $driver
 * @property int $amount
 * @property string $reference
 * @property string $status
 * @property ?string $notes
 * @property string $card_type
 * @property ?string $last_four
 * @property ?array $meta
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
 */
class Transaction extends BaseModel
{
    use HasFactory, HasMacros, LogsActivity;

    /**
     * {@inheritDoc}
     */
    protected $guarded = [];

    /**
     * {@inheritDoc}
     */
    protected $casts = [
        'refund' => 'bool',
        'amount' => Price::class,
        'meta'   => AsArrayObject::class,
    ];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TransactionFactory
    {
        return TransactionFactory::new();
    }

    /**
     * Return the order relationship.
     *
     * @return BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Return the currency relationship.
     *
     * @return HasOneThrough
     */
    public function currency(): HasOneThrough
    {
        return $this
            ->hasOneThrough(
                Currency::class,
                Order::class,
                'id',
                'code',
                'order_id',
                'currency_code'
            );
    }

    public function driver()
    {
        return Payments::driver($this->driver);
    }

    public function refund(int $amount, $notes = null)
    {
        return $this->driver()->refund($this, $amount, $notes);
    }

    public function capture(int $amount = 0)
    {
        return $this->driver()->capture($this, $amount);
    }
}
