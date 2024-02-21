<?php

namespace HeadlessEcom\Observers;

use HeadlessEcom\Jobs\Collections\UpdateProductPositions;
use HeadlessEcom\Models\Collection;

class CollectionObserver
{
    /**
     * Handle the Collection "updated" event.
     *
     * @return void
     */
    public function updated(Collection $collection)
    {
        UpdateProductPositions::dispatch($collection);
    }
}
