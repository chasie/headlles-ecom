<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\HasTranslations;
use HeadlessEcom\Database\Factories\AttributeGroupFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $attributable_type
 * @property string $name
 * @property string $handle
 * @property int $position
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
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
     * @return HasMany
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(Attribute::class)->orderBy('position');
    }
}
