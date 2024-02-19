<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasTranslations;
use Chasie\HeadlesEcom\Database\Factories\AttributeFactory;
use Chasie\HeadlesEcom\Facades\DB;

/**
 * @property int $id
 * @property string $attribute_type
 * @property int $attribute_group_id
 * @property int $position
 * @property string $name
 * @property string $handle
 * @property string $section
 * @property string $type
 * @property bool $required
 * @property ?string $default_value
 * @property string $configuration
 * @property bool $system
 * @property string $validation_rules
 * @property bool $filterable
 * @property bool $searchable
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Attribute extends BaseModel
{
    use HasFactory, HasMacros, HasTranslations;

    public static function boot()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        static::deleting(
            function (Model $model) use ($prefix)
            {
                DB::table("{$prefix}attributables")
                    ->where('attribute_id', '=', $model->id)
                    ->delete();
            }
        );
        parent::boot();
    }

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Define which attributes should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name'          => AsCollection::class,
        'configuration' => AsCollection::class,
    ];

    /**
     * Return the attribuable relation.
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Returns the attribute group relation.
     */
    public function attributeGroup(): BelongsTo
    {
        return $this->belongsTo(AttributeGroup::class);
    }

    /**
     * Apply the system scope to the query builder.
     *
     * @param  string  $type
     * @return void
     */
    public function scopeSystem(Builder $query, $type)
    {
        return $query->whereAttributeType($type)->whereSystem(true);
    }
}
