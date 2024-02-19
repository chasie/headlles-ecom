<?php

namespace Chasie\HeadlesEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Chasie\HeadlesEcom\Base\BaseModel;
use Chasie\HeadlesEcom\Base\Traits\HasDefaultRecord;
use Chasie\HeadlesEcom\Base\Traits\HasMacros;
use Chasie\HeadlesEcom\Base\Traits\LogsActivity;
use Chasie\HeadlesEcom\Database\Factories\ChannelFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property bool $default
 * @property ?string $url
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class Channel extends BaseModel
{
    use HasDefaultRecord,
        HasFactory,
        HasMacros,
        LogsActivity,
        SoftDeletes;

    /**
     * Return a new factory instance for the model.
     */
    protected static function newFactory(): ChannelFactory
    {
        return ChannelFactory::new();
    }

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Mutator for formatting the handle to a slug.
     *
     * @param  string  $val
     * @return void
     */
    public function setHandleAttribute($val)
    {
        $this->attributes['handle'] = Str::slug($val);
    }

    /**
     * Get the parent channelable model.
     */
    public function channelable()
    {
        return $this->morphTo();
    }
}
