<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\HasTranslations;
use Chasie\HeadlesEcom\Database\Factories\AttributeGroupFactory;

/**
 * @property int $id
 * @property string $attributable_type
 * @property string $name
 * @property string $handle
 * @property int $position
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class AttributeGroup extends BaseModel
{
    use HasFactory;
    use HasMacros;
    use HasTranslations;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): AttributeGroupFactory
    {
        return AttributeGroupFactory::new();
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
        'name' => AsCollection::class,
    ];

    /**
     * Return the attributes relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributes()
    {
        return $this->hasMany(Attribute::class)->orderBy('position');
    }
}
