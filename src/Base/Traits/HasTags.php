<?php

namespace HeadlessEcom\Base\Traits;

use Illuminate\Support\Collection;
use HeadlessEcom\Jobs\SyncTags;
use HeadlessEcom\Models\Tag;

trait HasTags
{
    /**
     * Get the tags
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany<Tag>
     */
    public function tags()
    {
        $prefix = config('headless-ecom.database.table_prefix');

        return $this->morphToMany(
            Tag::class,
            'taggable',
            "{$prefix}taggables"
        )->withTimestamps();
    }

    public function syncTags(Collection $tags)
    {
        SyncTags::dispatch($this, $tags);
    }
}
