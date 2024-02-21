<?php

namespace HeadlessEcom\Models;

use HeadlessEcom\Base\BaseModel;
use HeadlessEcom\Base\Traits\HasMedia as TraitsHasMedia;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;

/**
 * @property int $id
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 */
class Asset extends BaseModel implements HasMedia
{
    use TraitsHasMedia;

    /**
     * Define which attributes should be
     * protected from mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the associated file.
     *
     * @return MorphOne
     */
    public function file(): MorphOne
    {
        return $this
            ->morphOne(
                related: config('media-library.media_model'),
                name   : 'model'
            );
    }
}
