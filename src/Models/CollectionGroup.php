<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\CollectionGroupFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class CollectionGroup extends BaseModel
{
    use HasFactory, HasMacros;

    protected $guarded = [];

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): CollectionGroupFactory
    {
        return CollectionGroupFactory::new();
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }
}
