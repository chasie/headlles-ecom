<?php

namespace HeadlessEcom\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasDefaultRecord;
use HeadlessEcom\Base\Traits\HasMacros;
use HeadlessEcom\Base\Traits\LogsActivity;
use HeadlessEcom\Database\Factories\ChannelFactory;

/**
 * @property int $id
 * @property string $name
 * @property string $handle
 * @property bool $default
 * @property ?string $url
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 * @property ?Carbon $deleted_at
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
    public function setHandleAttribute(string $val): void
    {
        $this->attributes['handle'] = Str::slug($val);
    }

    /**
     * Get the parent channelable model.
     * @return MorphTo
     */
    public function channelable(): MorphTo
    {
        return $this->morphTo();
    }
}
