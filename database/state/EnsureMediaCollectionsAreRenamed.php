<?php

namespace HeadlessEcom\Database\State;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Schema;
use HeadlessEcom\Facades\DB;
use HeadlessEcom\Models\Brand;
use HeadlessEcom\Models\Collection;
use HeadlessEcom\Models\Product;

class EnsureMediaCollectionsAreRenamed
{
    public function prepare()
    {
        //
    }

    public function run(): void
    {
        if (! $this->shouldRun()) {
            return;
        }

        $this->getOutdatedMediaQuery()->update(['collection_name' => 'images']);
    }

    protected function shouldRun()
    {
        return Schema::hasTable('media') && $this->getOutdatedMediaQuery()->count();
    }

    /**
     * @return Builder
     */
    protected function getOutdatedMediaQuery(): Builder
    {
        return DB::table(app(config('media-library.media_model'))->getTable())
            ->whereIn('model_type', [Product::class, Collection::class, Brand::class])
            ->where('collection_name', 'products');
    }
}
