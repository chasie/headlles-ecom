<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Database\Factories\TagFactory;

/**
 * @property int $id
 * @property string $value
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 */
class Tag extends BaseModel
{
    use HasFactory,
        HasMacros;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): TagFactory
    {
        return TagFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    public function taggable(): MorphTo
    {
        return $this->morphTo();
    }

}
