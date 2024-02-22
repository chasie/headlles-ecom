<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Database\Factories\UrlFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $language_id
 * @property string $element_type
 * @property int $element_id
 * @property string $slug
 * @property bool $default
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Url extends BaseModel
{
    use HasFactory, HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): UrlFactory
    {
        return UrlFactory::new();
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
        'default' => 'boolean',
    ];

    /**
     * Return the element relationship.
     *
     * @return MorphTo
     */
    public function element(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Return the language relationship.
     *
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Return the query scope for default.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->whereDefault(true);
    }
}
